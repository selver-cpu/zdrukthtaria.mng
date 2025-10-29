<?php

namespace App\Http\Controllers;

use App\Models\Projektet;
use App\Services\CuttingOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CuttingOptimizationController extends Controller
{
    protected $cuttingService;
    
    public function __construct(CuttingOptimizationService $cuttingService)
    {
        $this->cuttingService = $cuttingService;
    }
    
    /**
     * Show cutting optimization page
     */
    public function index(Request $request)
    {
        $projektId = $request->get('projekt_id');
        $projekt = null;
        $visualization = [];
        
        if ($projektId) {
            $projekt = Projektet::with(['dimensions.materiali'])->findOrFail($projektId);
            $visualization = $this->cuttingService->generateVisualization($projekt);
        }
        
        $projektet = Projektet::orderBy('created_at', 'desc')->get();
        
        return view('cutting-optimization.index', compact('projektet', 'projekt', 'visualization'));
    }
    
    /**
     * Export project to XML
     */
    public function export(Projektet $projekt)
    {
        try {
            $xml = $this->cuttingService->exportToXML($projekt);
            
            $filename = 'cutting_plan_' . $projekt->projekt_id . '_' . date('Ymd_His') . '.xml';
            
            return response($xml)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Gabim gjatë eksportimit: ' . $e->getMessage());
        }
    }
    
    /**
     * Import XML cutting plan
     */
    public function import(Request $request, Projektet $projekt)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml|max:10240' // 10MB max
        ]);
        
        try {
            $file = $request->file('xml_file');
            $xmlContent = file_get_contents($file->getRealPath());
            
            $result = $this->cuttingService->importFromXML($xmlContent, $projekt);
            
            if ($result['success']) {
                $message = "U importuan me sukses {$result['imported']} dimensione.";
                if (!empty($result['errors'])) {
                    $message .= " Gabime: " . implode(', ', $result['errors']);
                }
                return back()->with('success', $message);
            } else {
                return back()->with('error', 'Gabim gjatë importimit: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gabim gjatë importimit: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate and download cutting plan visualization
     */
    public function visualize(Projektet $projekt)
    {
        $visualization = $this->cuttingService->generateVisualization($projekt);
        
        return view('cutting-optimization.visualize', compact('projekt', 'visualization'));
    }
}
