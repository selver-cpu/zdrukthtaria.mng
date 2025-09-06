<?php

namespace App\Http\Controllers;

use App\Models\Projektet;
use App\Models\ProjektMateriale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjektMaterialeController extends Controller
{
    public function store(Request $request, Projektet $projekt)
    {
        try {
            $request->validate([
                'material_id' => [
                    'required',
                    'exists:materialet,material_id',
                    // Sigurohemi qe i njejti material te mos shtohet dy here per te njejtin projekt
                    Rule::unique('projekt_materiale')->where(function ($query) use ($projekt) {
                        return $query->where('projekt_id', $projekt->projekt_id);
                    }),
                ],
                'sasia_perdorur' => 'required|numeric|min:0.01',
            ], [
                'material_id.unique' => 'Ky material është shtuar tashmë në këtë projekt.',
            ]);

            $projekt->materialet()->attach($request->material_id, ['sasia_perdorur' => $request->sasia_perdorur]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Materiali u shtua me sukses në projekt.'
                ]);
            }
            
            return back()->with('success', 'Materiali u shtua me sukses në projekt.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request, $material_id)
    {
        // Gjej projektin aktual nga URL
        $referer = $request->headers->get('referer');
        $segments = explode('/', $referer);
        $projekt_id = null;
        
        // Përpiqemi të gjejmë projekt_id nga URL
        foreach ($segments as $key => $segment) {
            if ($segment === 'projektet' && isset($segments[$key + 1])) {
                $projekt_id = $segments[$key + 1];
                break;
            }
        }
        
        if (!$projekt_id) {
            return back()->with('error', 'Nuk mund të identifikohej projekti.');
        }
        
        // Gjej materialin e projektit bazuar në projekt_id dhe material_id
        $projektMaterial = ProjektMateriale::where('projekt_id', $projekt_id)
                                       ->where('material_id', $material_id)
                                       ->first();
        
        if (!$projektMaterial) {
            return back()->with('error', 'Materiali nuk u gjet në projekt.');
        }
        
        $projektMaterial->delete();

        return back()->with('success', 'Materiali u hoq me sukses nga projekti.');
    }
}
