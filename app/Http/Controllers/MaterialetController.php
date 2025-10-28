<?php

namespace App\Http\Controllers;

use App\Models\Materialet;
use Illuminate\Http\Request;

class MaterialetController extends Controller
{
    /**
     * Kontrollon nëse përdoruesi ka të drejtë të menaxhojë materialet
     */
    protected function checkMaterialManagementAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Ju lutem kyçuni për të vazhduar.');
        }
        
        // Vetëm Admin (1) dhe Menaxher (2) mund të shtojnë/modifikojnë/fshijnë materiale
        if (!in_array(auth()->user()->rol_id, [1, 2])) {
            return redirect()->route('materialet.index')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të menaxhojnë materialet.');
        }
        
        return null;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materialet = Materialet::orderBy('emri_materialit')->paginate(15);
        return view('materialet.index', compact('materialet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkMaterialManagementAccess()) {
            return $redirect;
        }
        
        return view('materialet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkMaterialManagementAccess()) {
            return $redirect;
        }
        
        $validatedData = $request->validate([
            'emri' => 'required|string|max:255|unique:materialet,emri_materialit',
            'pershkrimi' => 'nullable|string',
            'njesia_matese' => 'required|string|max:50',
            'sasia_stokut' => 'nullable|numeric|min:0',
            'sasia_minimale' => 'nullable|numeric|min:0',
            'cmimi_per_njesi' => 'nullable|numeric|min:0',
            'lokacioni' => 'nullable|string|max:255',
        ]);
        
        // Map field names for database
        $validatedData['emri_materialit'] = $validatedData['emri'];
        unset($validatedData['emri']);

        Materialet::create($validatedData);

        return redirect()->route('materialet.index')->with('success', 'Materiali u shtua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materialet $materialet)
    {
        // Not used for this resource
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materialet $materialet)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkMaterialManagementAccess()) {
            return $redirect;
        }
        
        return view('materialet.edit', compact('materialet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materialet $materialet)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkMaterialManagementAccess()) {
            return $redirect;
        }
        
        $validatedData = $request->validate([
            'emri' => 'required|string|max:255|unique:materialet,emri_materialit,' . $materialet->material_id . ',material_id',
            'pershkrimi' => 'nullable|string',
            'njesia_matese' => 'required|string|max:50',
            'sasia_stokut' => 'nullable|numeric|min:0',
            'sasia_minimale' => 'nullable|numeric|min:0',
            'cmimi_per_njesi' => 'nullable|numeric|min:0',
            'lokacioni' => 'nullable|string|max:255',
        ]);
        
        // Map field names for database
        $validatedData['emri_materialit'] = $validatedData['emri'];
        unset($validatedData['emri']);

        $materialet->update($validatedData);

        return redirect()->route('materialet.index')->with('success', 'Materiali u modifikua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materialet $materialet)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkMaterialManagementAccess()) {
            return $redirect;
        }
        
        // Fshij materialin pa kontrolluar nëse është në përdorim
        try {
            // Shkëput materialin nga të gjitha projektet para se ta fshish
            $materialet->projektet()->detach();
            
            // Fshij materialin
            $materialet->delete();
            
            return redirect()->route('materialet.index')->with('success', 'Materiali u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('materialet.index')->with('error', 'Ndodhi një gabim gjatë fshirjes së materialit: ' . $e->getMessage());
        }
    }
}
