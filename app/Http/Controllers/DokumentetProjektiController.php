<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Projektet;
use App\Models\DokumentetProjekti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumentetProjektiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Projektet $projekt)
    {
        $request->validate([
            'dokument' => 'required|file|mimes:jpg,jpeg,png,gif,webp,bmp,svg,pdf,doc,docx,xls,xlsx,zip,tar,gz,7z,stl,step,skp,dwg,3ds,obj,fbx,dae,rar,txt,csv,html,htm,css,js,json,xml,md,ppt,pptx,rtf,odt|max:512000', // max 500MB
            'pershkrimi' => 'nullable|string|max:255',
            'kategoria' => 'nullable|string|in:vizatim,dimension,material,3d_model,excel,foto,prezantim,arkiv,tjeter',
        ]);

        $file = $request->file('dokument');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('dokumentet_projekti/' . $projekt->projekt_id, $filename, 'public');

        // Kategorizojmë automatikisht skedarin bazuar në prapashtesën e tij
        $extension = strtolower($file->getClientOriginalExtension());
        $kategoria = $request->kategoria;
        
        if (!$kategoria) {
            // Dokumentet
            if (in_array($extension, ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'])) {
                $kategoria = 'vizatim';
            } 
            // Excel dhe tabela
            elseif (in_array($extension, ['xls', 'xlsx', 'csv', 'ods'])) {
                $kategoria = 'excel';
            } 
            // Modelet 3D dhe vizatimet teknike
            elseif (in_array($extension, ['stl', 'step', 'skp', 'dwg', '3ds', 'obj', 'fbx', 'dae'])) {
                $kategoria = '3d_model';
            } 
            // Imazhet
            elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'])) {
                $kategoria = 'foto';
            }
            // Prezantimet
            elseif (in_array($extension, ['ppt', 'pptx', 'odp'])) {
                $kategoria = 'prezantim';
            }
            // Fajllat e kompresuar
            elseif (in_array($extension, ['zip', 'rar', 'tar', 'gz', '7z'])) {
                $kategoria = 'arkiv';
            }
            // Të tjera
            else {
                $kategoria = 'tjeter';
            }
        }

        $projekt->dokumentet()->create([
            'emri_skedarit' => $file->getClientOriginalName(),
            'rruga_skedarit' => $path,
            'lloji_skedarit' => $file->getClientMimeType(),
            'madhesia_skedarit' => $file->getSize(),
            'pershkrimi' => $request->pershkrimi,
            'kategoria' => $kategoria,
            'perdorues_id_ngarkues' => Auth::id(),
        ]);

        return back()->with('success', 'Dokumenti u ngarkua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dokument = DokumentetProjekti::findOrFail($id);
        return view('dokumentet.show', compact('dokument'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dokument = DokumentetProjekti::findOrFail($id);
        
        $request->validate([
            'pershkrimi' => 'nullable|string|max:255',
            'kategoria' => 'nullable|string|in:vizatim,dimension,material,3d_model,excel,foto,prezantim,arkiv,tjeter',
        ]);
        
        $dokument->update([
            'pershkrimi' => $request->pershkrimi,
            'kategoria' => $request->kategoria,
        ]);
        
        return back()->with('success', 'Informacioni i dokumentit u përditësua me sukses.');
    }

    /**
     * Download the specified resource.
     */
    /**
     * View the specified resource directly in browser.
     */
    public function view(Projektet $projekt, string $id)
    {
        $dokument = DokumentetProjekti::where('projekt_id', $projekt->projekt_id)
                                     ->where('dokument_id', $id)
                                     ->firstOrFail();
        
        // Kontrollo nëse përdoruesi ka të drejta për të parë dokumentin
        if (Auth::id() !== $dokument->perdorues_id_ngarkues && 
            !in_array(Auth::user()->rol_id ?? 0, [1, 2])) { // 1 = admin, 2 = menaxher
            abort(403, 'Nuk keni leje për të parë këtë dokument.');
        }
        
        $path = storage_path('app/public/' . $dokument->rruga_skedarit);
        
        if (!file_exists($path)) {
            abort(404, 'Skedari nuk u gjet.');
        }
        
        $mime = mime_content_type($path);
        $fileName = basename($path);
        
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];
        
        return response()->file($path, $headers);
    }

    /**
     * Download the specified resource.
     */
    public function download(Projektet $projekt, string $id)
    {
        $dokument = DokumentetProjekti::where('projekt_id', $projekt->projekt_id)
                                     ->where('dokument_id', $id)
                                     ->firstOrFail();
        
        // Kontrollo nëse përdoruesi ka të drejta për të shkarkuar dokumentin
        if (Auth::id() !== $dokument->perdorues_id_ngarkues && 
            !in_array(Auth::user()->rol_id ?? 0, [1, 2])) {
            return back()->with('error', 'Nuk keni të drejta për të shkarkuar këtë dokument.');
        }
        
        // Shkarko skedarin fizik
        if (Storage::disk('public')->exists($dokument->rruga_skedarit)) {
            return response()->download(storage_path('app/public/' . $dokument->rruga_skedarit), $dokument->emri_skedarit);
        } else {
            return back()->with('error', 'Skedari nuk ekziston në server.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Projektet $projekt, string $id)
    {
        try {
            $dokument = DokumentetProjekti::where('projekt_id', $projekt->projekt_id)
                                         ->where('dokument_id', $id)
                                         ->firstOrFail();
            
            // Kontrollo nëse përdoruesi ka të drejta për të fshirë dokumentin
            if (Auth::id() !== $dokument->perdorues_id_ngarkues && 
                !in_array(Auth::user()->rol_id ?? 0, [1, 2])) { // 1 = admin, 2 = menaxher
                return back()->with('error', 'Nuk keni të drejta për të fshirë këtë dokument.');
            }
            // Fshi skedarin fizik
            if (Storage::disk('public')->exists($dokument->rruga_skedarit)) {
                Storage::disk('public')->delete($dokument->rruga_skedarit);
            }
            
            // Fshi rekordin nga databaza
            $dokument->delete();
            
            return back()->with('success', 'Dokumenti u fshi me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ndodhi një gabim gjatë fshirjes së dokumentit: ' . $e->getMessage());
        }
    }
}
