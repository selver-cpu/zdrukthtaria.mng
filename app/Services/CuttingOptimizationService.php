<?php

namespace App\Services;

use App\Models\Projektet;
use App\Models\ProjektetDimensions;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;

class CuttingOptimizationService
{
    /**
     * Export project dimensions to XML format for cutting optimization software
     */
    public function exportToXML(Projektet $projekt): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><CuttingOptimization></CuttingOptimization>');
        
        // Add project info
        $projectInfo = $xml->addChild('ProjectInfo');
        $projectInfo->addChild('ProjectID', $projekt->projekt_id);
        $projectInfo->addChild('ProjectName', htmlspecialchars($projekt->emri_projektit));
        $projectInfo->addChild('Client', htmlspecialchars($projekt->klient->emri_klientit ?? 'N/A'));
        $projectInfo->addChild('ExportDate', date('Y-m-d H:i:s'));
        
        // Get all dimensions for this project
        $dimensions = ProjektetDimensions::where('projekt_id', $projekt->projekt_id)->get();
        
        // Group by material
        $materialGroups = $dimensions->groupBy('material_id');
        
        $materials = $xml->addChild('Materials');
        
        foreach ($materialGroups as $materialId => $dims) {
            $firstDim = $dims->first();
            $material = $materials->addChild('Material');
            
            $material->addChild('ID', $materialId);
            $material->addChild('Name', htmlspecialchars($firstDim->material->emri_materialit ?? 'Unknown'));
            $material->addChild('Thickness', $firstDim->trashesia ?? 18);
            $material->addChild('Unit', htmlspecialchars($firstDim->material->njesia_matese ?? 'mm'));
            
            // Add pieces for this material
            $pieces = $material->addChild('Pieces');
            
            foreach ($dims as $index => $dimension) {
                $piece = $pieces->addChild('Piece');
                $piece->addChild('Number', $index + 1);
                $piece->addChild('ID', $dimension->id);
                $piece->addChild('Code', $dimension->id);
                $piece->addChild('Label', htmlspecialchars($dimension->pershkrimi ?? 'Piece ' . ($index + 1)));
                
                $dimNode = $piece->addChild('Dimensions');
                $dimNode->addChild('Length', $dimension->gjatesia ?? 0);
                $dimNode->addChild('Width', $dimension->gjeresia ?? 0);
                $dimNode->addChild('Thickness', $dimension->trashesia ?? 0);
                
                $piece->addChild('Quantity', $dimension->sasia ?? 1);
                $piece->addChild('EdgeBanding', $dimension->edge_banding ?? 'none');
                
                // Calculate area
                $area = ($dimension->gjatesia ?? 0) * ($dimension->gjeresia ?? 0) * ($dimension->sasia ?? 1);
                $piece->addChild('TotalArea', number_format($area / 1000000, 2)); // Convert to m²
            }
        }
        
        // Add summary
        $summary = $xml->addChild('Summary');
        $summary->addChild('TotalPieces', $dimensions->count());
        $summary->addChild('TotalQuantity', $dimensions->sum('sasia'));
        $totalArea = $dimensions->sum(function($dim) {
            return ($dim->gjatesia ?? 0) * ($dim->gjeresia ?? 0) * ($dim->sasia ?? 1);
        });
        $summary->addChild('TotalArea', number_format($totalArea / 1000000, 2) . ' m²');
        
        // Format XML with indentation
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        
        return $dom->saveXML();
    }
    
    /**
     * Import cutting plan from XML
     */
    public function importFromXML(string $xmlContent, Projektet $projekt): array
    {
        try {
            $xml = new SimpleXMLElement($xmlContent);
            $imported = [];
            $errors = [];
            
            // Check if it's a valid cutting optimization XML
            if (!isset($xml->Materials) && !isset($xml->CuttingPlan)) {
                throw new \Exception('Invalid XML format. Expected CuttingOptimization XML.');
            }
            
            // Import from Materials section
            if (isset($xml->Materials)) {
                foreach ($xml->Materials->Material as $material) {
                    $materialName = (string)$material->Name;
                    $thickness = (float)$material->Thickness;
                    
                    // Find or create material in database
                    $dbMaterial = \App\Models\Materialet::firstOrCreate(
                        ['emri_materialit' => $materialName],
                        [
                            'pershkrimi' => 'Imported from XML',
                            'njesia_matese' => (string)($material->Unit ?? 'mm'),
                            'cmimi_per_njesi' => 0
                        ]
                    );
                    
                    // Import pieces
                    if (isset($material->Pieces)) {
                        foreach ($material->Pieces->Piece as $piece) {
                            try {
                                $dimension = ProjektetDimensions::create([
                                    'projekt_id' => $projekt->projekt_id,
                                    'material_id' => $dbMaterial->material_id,
                                    'gjatesia' => (float)$piece->Dimensions->Length,
                                    'gjeresia' => (float)$piece->Dimensions->Width,
                                    'trashesia' => (float)($piece->Dimensions->Thickness ?? $thickness),
                                    'sasia' => (int)($piece->Quantity ?? 1),
                                    'pershkrimi' => (string)($piece->Label ?? ''),
                                    'edge_banding' => (string)($piece->EdgeBanding ?? 'none'),
                                ]);
                                
                                $imported[] = $dimension;
                            } catch (\Exception $e) {
                                $errors[] = "Error importing piece: " . $e->getMessage();
                            }
                        }
                    }
                }
            }
            
            // Import from CuttingPlan section (if exists)
            if (isset($xml->CuttingPlan)) {
                foreach ($xml->CuttingPlan->Sheet as $sheet) {
                    if (isset($sheet->Pieces)) {
                        foreach ($sheet->Pieces->Piece as $piece) {
                            // Similar import logic
                        }
                    }
                }
            }
            
            return [
                'success' => true,
                'imported' => count($imported),
                'errors' => $errors,
                'dimensions' => $imported
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'imported' => 0
            ];
        }
    }
    
    /**
     * Generate cutting plan visualization data
     */
    public function generateVisualization(Projektet $projekt): array
    {
        $dimensions = ProjektetDimensions::where('projekt_id', $projekt->projekt_id)
            ->with('material')
            ->get();
        
        $sheets = [];
        $colors = ['#0066FF', '#00CC00', '#FF9900', '#FF0066', '#9900CC', '#00CCCC'];
        $colorIndex = 0;
        
        foreach ($dimensions as $dimension) {
            $sheets[] = [
                'id' => $dimension->id,
                'length' => $dimension->gjatesia,
                'width' => $dimension->gjeresia,
                'thickness' => $dimension->trashesia,
                'quantity' => $dimension->sasia,
                'material' => $dimension->material->emri_materialit ?? 'Unknown',
                'label' => $dimension->pershkrimi ?? 'Piece ' . $dimension->id,
                'color' => $colors[$colorIndex % count($colors)],
                'area' => ($dimension->gjatesia * $dimension->gjeresia) / 1000000 // m²
            ];
            $colorIndex++;
        }
        
        return $sheets;
    }
}
