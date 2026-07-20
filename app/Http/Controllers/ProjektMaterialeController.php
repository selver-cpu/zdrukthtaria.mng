<?php

namespace App\Http\Controllers;

use App\Models\Projektet;
use App\Models\ProjektMateriale;
use App\Traits\LogsProjectActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjektMaterialeController extends Controller
{
    use LogsProjectActions;

    public function store(Request $request, Projektet $projekt)
    {
        try {
            $validated = $request->validate([
                'material_id' => [
                    'required',
                    'exists:materialet,material_id',
                    // Sigurohemi qe i njejti material te mos shtohet dy here per te njejtin projekt
                    Rule::unique('projekt_materiale')->where(function ($query) use ($projekt) {
                        return $query->where('projekt_id', $projekt->projekt_id);
                    }),
                ],
                'sasia_perdorur' => 'required|numeric|min:0.01',
                'pershkrimi' => 'nullable|string',
            ], [
                'material_id.unique' => 'Ky material është shtuar tashmë në këtë projekt.',
            ]);

            // projekt_materiale only has sasia_perdorur as pivot data
            $projekt->materialet()->attach($validated['material_id'], [
                'sasia_perdorur' => $validated['sasia_perdorur'],
            ]);

            $this->logProjectAction(
                $projekt->projekt_id,
                'Materiali u shtua në projekt',
                [
                    'material_id' => $validated['material_id'],
                    'sasia_perdorur' => $validated['sasia_perdorur'],
                    'pershkrimi' => $validated['pershkrimi'] ?? null,
                ]
            );
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Materiali u shtua me sukses në projekt.'
                ]);
            }
            
            return back()->with('success', 'Materiali u shtua me sukses në projekt.');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Të dhënat nuk janë valide',
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        } catch (\Exception $e) {
            Log::error('Error adding material to project: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ndodhi një gabim gjatë ruajtjes së materialit.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Ndodhi një gabim gjatë shtimit të materialit.'])->withInput();
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
