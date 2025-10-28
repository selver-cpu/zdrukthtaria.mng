<?php

namespace App\Http\Controllers;

use App\Models\Klientet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KlientetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $klientet = Klientet::latest('data_krijimit')->paginate(10);
        return view('klientet.index', compact('klientet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lejo vetëm Admin (1), Menaxher (2) dhe Disajnere (5)
        if (!auth()->check() || !in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('klientet.index')
                ->with('error', 'Nuk keni të drejtë të shtoni klientë.');
        }

        return view('klientet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lejo vetëm Admin (1), Menaxher (2) dhe Disajnere (5)
        if (!auth()->check() || !in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('klientet.index')
                ->with('error', 'Nuk keni të drejtë të shtoni klientë.');
        }
        
        $validatedData = $request->validate([
            'emri_klientit' => 'required|string|max:255',
            'person_kontakti' => 'nullable|string|max:255',
            'email_kontakt' => 'required|string|email|max:255|unique:klientet,email_kontakt',
            'telefon_kontakt' => 'required|string|max:20',
            'adresa_faktura' => 'required|string|max:255',
            'qyteti' => 'required|string|max:100',
            'kodi_postal' => 'required|string|max:20',
            'shteti' => 'required|string|max:100',
            'shenime' => 'nullable|string',
        ]);

        Klientet::create($validatedData);

        return redirect()->route('klientet.index')->with('success', 'Klienti u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Klientet $klientet)
    {
        //
    }

    public function edit(Klientet $klientet)
    {
        // Lejo vetëm Admin (1), Menaxher (2) dhe Disajnere (5)
        if (!auth()->check() || !in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('klientet.index')
                ->with('error', 'Nuk keni të drejtë të modifikoni klientët.');
        }
        
        return view('klientet.edit', compact('klientet'));
    }

    public function update(Request $request, Klientet $klientet)
    {
        // Lejo vetëm Admin (1), Menaxher (2) dhe Disajnere (5)
        if (!auth()->check() || !in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('klientet.index')
                ->with('error', 'Nuk keni të drejtë të modifikoni klientët.');
        }
        
        $validatedData = $request->validate([
            'emri_klientit' => 'required|string|max:255',
            'person_kontakti' => 'nullable|string|max:255',
            'email_kontakt' => 'required|string|email|max:255|unique:klientet,email_kontakt,' . $klientet->klient_id . ',klient_id',
            'telefon_kontakt' => 'required|string|max:20',
            'adresa_faktura' => 'required|string|max:255',
            'qyteti' => 'required|string|max:100',
            'kodi_postal' => 'required|string|max:20',
            'shteti' => 'required|string|max:100',
            'shenime' => 'nullable|string',
        ]);

        $klientet->update($validatedData);

        return redirect()->route('klientet.index')->with('success', 'Klienti u modifikua me sukses.');
    }

    public function destroy(Klientet $klientet)
    {
        // Lejo vetëm Admin (1), Menaxher (2) dhe Disajnere (5)
        if (!auth()->check() || !in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('klientet.index')
                ->with('error', 'Nuk keni të drejtë të fshini klientë.');
        }
        
        try {
            // Ruaj detajet e klientit para fshirjes për logging
            $clientDetails = [
                'klient_id' => $klientet->klient_id,
                'person_kontakti' => $klientet->person_kontakti,
                'email_kontakt' => $klientet->email_kontakt
            ];
            
            // Kontrollo nëse klienti ka projekte
            $projectCount = DB::table('projektet')
                ->where('klient_id', $klientet->klient_id)
                ->count();
                
            if ($projectCount > 0) {
                return redirect()->route('klientet.index')
                    ->with('error', 'Klienti nuk mund të fshihet sepse ka ' . $projectCount . ' projekte aktive.');
            }
            
            // Fshij klientin (do të përdorë soft delete për shkak të trait-it)
            $klientet->delete();
            
            Log::info('Klienti u fshi me sukses', $clientDetails);
            
            return redirect()->route('klientet.index')
                ->with('success', 'Klienti u fshi me sukses.');
        } catch (\Exception $e) {
            Log::error('Gabim gjatë fshirjes së klientit', [
                'error' => $e->getMessage(),
                'client_id' => $klientet->klient_id
            ]);
            return redirect()->route('klientet.index')
                ->with('error', 'Ndodhi një gabim gjatë fshirjes së klientit. Ju lutem provoni përsëri.');
        }
    }
}
