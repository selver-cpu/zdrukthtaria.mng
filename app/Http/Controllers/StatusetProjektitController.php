<?php

namespace App\Http\Controllers;

use App\Models\StatusetProjektit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusetProjektitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuset = StatusetProjektit::orderBy('renditja')->paginate(10);
        return view('statuset.index', compact('statuset'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('statuset.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'emri_statusit' => 'required|string|max:255|unique:Statuset_Projektit,emri_statusit',
            'pershkrimi' => 'nullable|string',
            'renditja' => 'required|integer',
        ]);

        StatusetProjektit::create($validatedData);

        return redirect()->route('statuset.index')->with('success', 'Statusi u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StatusetProjektit $statuset)
    {
        // Not used for this resource
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StatusetProjektit $statuset)
    {
        return view('statuset.edit', compact('statuset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StatusetProjektit $statuset)
    {
        $validatedData = $request->validate([
            'emri_statusit' => 'required|string|max:255|unique:Statuset_Projektit,emri_statusit,' . $statuset->status_id . ',status_id',
            'pershkrimi' => 'nullable|string',
            'renditja' => 'required|integer',
        ]);

        $statuset->update($validatedData);

        return redirect()->route('statuset.index')->with('success', 'Statusi u modifikua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StatusetProjektit $statuset)
    {
        try {
            // Check if any projects are using this status
            $projectCount = DB::table('projektet')
                ->where('status_id', $statuset->status_id)
                ->count();
                
            if ($projectCount > 0) {
                $message = $projectCount === 1
                    ? 'Ky status nuk mund të fshihet sepse është në përdorim nga 1 projekt.'
                    : 'Ky status nuk mund të fshihet sepse është në përdorim nga ' . $projectCount . ' projekte.';
                    
                return redirect()->route('statuset.index')->with('error', $message);
            }
            
            $statuset->delete();
            
            return redirect()->route('statuset.index')
                ->with('success', 'Statusi u fshi me sukses.');
        } catch (\Exception $e) {
            Log::error('Error deleting project status: ' . $e->getMessage());
            return redirect()->route('statuset.index')
                ->with('error', 'Ndodhi një gabim gjatë fshirjes së statusit. Ju lutem provoni përsëri.');
        }
    }
}
