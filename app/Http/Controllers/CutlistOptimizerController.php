<?php

namespace App\Http\Controllers;

use App\Models\ProjektetDimensions;
use App\Models\Materialet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutlistOptimizerController extends Controller
{
    /**
     * Shfaq faqen e optimizer-it
     */
    public function index(Request $request)
    {
        // Vetëm Admin, Menaxher dhe Disajnere
        if (!in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('dashboard')
                ->with('error', 'Nuk keni qasje në Cutlist Optimizer.');
        }

        // Merr të gjitha dimensionet që nuk janë optimizuar
        $dimensions = ProjektetDimensions::with(['projekt', 'materiali'])
            ->where('statusi_prodhimit', '!=', 'completed')
            ->latest()
            ->get();

        // Grup sipas materialit
        $groupedByMaterial = $dimensions->groupBy('materiali_id');

        $materialet = Materialet::all();

        return view('cutlist-optimizer.index', compact('groupedByMaterial', 'materialet'));
    }

    /**
     * Ekzekuto optimizimin
     */
    public function optimize(Request $request)
    {
        // Vetëm Admin, Menaxher dhe Disajnere
        if (!in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'dimension_ids' => 'required|array',
            'dimension_ids.*' => 'exists:projektet_dimensions,id',
            'stock_width' => 'required|numeric|min:100',
            'stock_height' => 'required|numeric|min:100',
            'saw_kerf' => 'nullable|numeric|min:0|max:10',
            'check_stock' => 'nullable|boolean',
        ]);

        // Merr dimensionet
        $dimensions = ProjektetDimensions::with(['projekt', 'materiali'])
            ->whereIn('id', $validated['dimension_ids'])
            ->get();

        // Kontrollo materialin dhe stokun
        $materialIds = $dimensions->pluck('materiali_id')->unique();
        $checkStock = $validated['check_stock'] ?? true;
        $stockWarnings = [];
        
        // Nëse ka më shumë se 1 material
        if ($materialIds->count() > 1) {
            $stockWarnings[] = "Ke zgjedhur pjesë me " . $materialIds->count() . " materiale të ndryshme. Rekomandohet optimizimi me një material.";
        }
        
        // Kontrollo stokun (nëse është aktivizuar)
        if ($checkStock && $materialIds->count() > 0) {
            foreach ($materialIds as $materialId) {
                $material = \App\Models\Materialet::find($materialId);
                if ($material && isset($material->sasia_ne_stok)) {
                    $neededSheets = ceil($dimensions->where('materiali_id', $materialId)->sum(function($dim) {
                        return ($dim->gjatesia * $dim->gjeresia * $dim->sasia) / 1000000; // m²
                    }) / (($validated['stock_width'] * $validated['stock_height']) / 1000000));
                    
                    if ($material->sasia_ne_stok < $neededSheets) {
                        $stockWarnings[] = "Material '{$material->emri_materialit}': Në stok ka {$material->sasia_ne_stok} tabaka, por nevojiten ~{$neededSheets} tabaka.";
                    }
                }
            }
        }

        // Përgatit të dhënat për optimizer
        $pieces = [];
        $errors = [];
        
        foreach ($dimensions as $dim) {
            // Validim: pjesa duhet të jetë më e vogël se tabaka
            if ($dim->gjatesia > $validated['stock_width'] || $dim->gjeresia > $validated['stock_height']) {
                // Try rotated
                if ($dim->gjeresia > $validated['stock_width'] || $dim->gjatesia > $validated['stock_height']) {
                    $errors[] = "Pjesa '{$dim->emri_pjeses}' ({$dim->gjatesia}×{$dim->gjeresia}mm) është më e madhe se tabaka ({$validated['stock_width']}×{$validated['stock_height']}mm)";
                    continue;
                }
            }
            
            for ($i = 0; $i < $dim->sasia; $i++) {
                $pieces[] = [
                    'id' => $dim->id . '_' . ($i + 1),
                    'name' => $dim->emri_pjeses,
                    'width' => (float) $dim->gjatesia,
                    'height' => (float) $dim->gjeresia,
                    'thickness' => (float) $dim->trashesia,
                    'quantity' => 1,
                    'material' => $dim->materiali->emri_materialit ?? 'N/A',
                    'edge_banding' => [
                        'front' => (bool) $dim->kantim_front,
                        'back' => (bool) $dim->kantim_back,
                        'left' => (bool) $dim->kantim_left,
                        'right' => (bool) $dim->kantim_right,
                    ]
                ];
            }
        }
        
        // Nëse ka gabime, kthe mesazh
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'error' => 'Disa pjesë janë shumë të mëdha për tabakën',
                'details' => $errors
            ], 400);
        }
        
        // Nëse nuk ka pjesë të vlefshme
        if (empty($pieces)) {
            return response()->json([
                'success' => false,
                'error' => 'Nuk ka pjesë të vlefshme për optimizim'
            ], 400);
        }

        // Krijo input file për Node.js
        $input = [
            'stockWidth' => (int) $validated['stock_width'],
            'stockHeight' => (int) $validated['stock_height'],
            'sawKerf' => (int) ($validated['saw_kerf'] ?? 4),
            'pieces' => $pieces
        ];

        // Log input për debugging
        \Log::info('Cutlist Optimizer Input', [
            'pieces_count' => count($pieces),
            'stock_size' => $validated['stock_width'] . 'x' . $validated['stock_height'],
            'first_piece' => !empty($pieces) ? $pieces[0] : null
        ]);

        $inputFile = storage_path('app/cutlist_input_' . time() . '.json');
        file_put_contents($inputFile, json_encode($input));

        // Ekzekuto optimizer (Python - Industrial Grade)
        $optimizerPath = base_path('cutlist-optimizer/optimizer.py');
        
        // Check if optimizer exists
        if (!file_exists($optimizerPath)) {
            return response()->json([
                'success' => false,
                'error' => 'Optimizer script not found',
                'path' => $optimizerPath
            ], 500);
        }
        
        $command = "python3 {$optimizerPath} {$inputFile} 2>&1";
        $output = shell_exec($command);

        // Log për debugging
        \Log::info('Cutlist Optimizer executed', [
            'command' => $command,
            'output' => $output,
            'input_file' => $inputFile
        ]);

        // Ruaj për debugging
        copy($inputFile, storage_path('app/cutlist_last_input.json'));
        
        // Fshi input file
        @unlink($inputFile);

        // Parse rezultati
        $result = json_decode($output, true);

        if (!$result) {
            return response()->json([
                'success' => false,
                'error' => 'Optimizer failed to parse output',
                'output' => substr($output, 0, 500), // First 500 chars
                'command' => $command
            ], 500);
        }

        return response()->json([
            'success' => true,
            'result' => $result,
            'warnings' => $stockWarnings
        ]);
    }

    /**
     * Shfaq rezultatet
     */
    public function showResult($id)
    {
        // TODO: Implement result viewing
        return view('cutlist-optimizer.result');
    }
}
