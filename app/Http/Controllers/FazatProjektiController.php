<?php

namespace App\Http\Controllers;

use App\Models\FazatProjekti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FazatProjektiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fazat = FazatProjekti::orderBy('renditja')->paginate(15);
        return view('fazat-projekti.index', compact('fazat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fazat-projekti.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'emri_fazes' => 'required|string|max:255|unique:fazat_projekti,emri_fazes',
            'pershkrimi' => 'nullable|string',
            'renditja' => 'required|integer|min:0',
        ]);

        FazatProjekti::create($request->all());

        return redirect()->route('fazat-projekti.index')->with('success', 'Faza e re u shtua me sukses.');
    }

    /**
     * Display the specified resource.
     */
     

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FazatProjekti $fazat_projekti)
    {
        return view('fazat-projekti.edit', ['faza' => $fazat_projekti]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FazatProjekti $fazat_projekti)
    {
        $request->validate([
            'emri_fazes' => 'required|string|max:255|unique:fazat_projekti,emri_fazes,'.$fazat_projekti->id,
            'pershkrimi' => 'nullable|string',
            'renditja' => 'required|integer|min:0',
        ]);

        $fazat_projekti->update($request->all());

        return redirect()->route('fazat-projekti.index')->with('success', 'Faza u modifikua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FazatProjekti $fazat_projekti)
    {
        // Kontrollo nëse faza është e lidhur me ndonjë projekt
        $projektCount = DB::table('projekt_faza_pune')
            ->where('faza_id', $fazat_projekti->faza_id)
            ->count();
            
        if ($projektCount > 0) {
            return back()->with('error', 'Kjo fazë nuk mund të fshihet sepse është e lidhur me ' . $projektCount . ' projekt(e).');
        }

        $fazat_projekti->delete();

        return redirect()->route('fazat-projekti.index')->with('success', 'Faza u fshi me sukses.');
    }
}
