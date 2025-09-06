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
        return view('klientet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if this request has already been processed
        $submissionToken = $request->session()->get('submission_token');
        $requestToken = $request->input('_token');
        
        if ($submissionToken === $requestToken) {
            return redirect()->route('klientet.index')->with('warning', 'Kërkesa juaj është duke u procesuar.');
        }
        
        // Store the current token
        $request->session()->put('submission_token', $requestToken);
        
        $validatedData = $request->validate([
            'emri' => 'required|string|max:255',
            'person_kontakti' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:Klientet,email',
            'telefon' => 'required|string|max:20',
            'adresa_faturimit' => 'required|string|max:255',
            'qyteti' => 'required|string|max:100',
            'kodi_postar' => 'required|string|max:20',
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
        return view('klientet.edit', compact('klientet'));
    }

    public function update(Request $request, Klientet $klientet)
    {
        $validatedData = $request->validate([
            'emri' => 'required|string|max:255',
            'person_kontakti' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:Klientet,email,' . $klientet->klient_id . ',klient_id',
            'telefon' => 'required|string|max:20',
            'adresa_faturimit' => 'required|string|max:255',
            'qyteti' => 'required|string|max:100',
            'kodi_postar' => 'required|string|max:20',
            'shteti' => 'required|string|max:100',
            'shenime' => 'nullable|string',
        ]);

        $klientet->update($validatedData);

        return redirect()->route('klientet.index')->with('success', 'Klienti u modifikua me sukses.');
    }

    public function destroy(Klientet $klientet)
    {
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
