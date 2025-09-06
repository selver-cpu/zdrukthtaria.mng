<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class StafiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stafi = User::orderBy('emri')->paginate(15);
        return view('stafi.index', compact('stafi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stafi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'emri' => ['required', 'string', 'max:255'],
            'mbiemri' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:perdoruesit,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol_id' => ['required', 'exists:rolet,rol_id'],
        ]);

        User::create([
            'emri' => $request->emri,
            'mbiemri' => $request->mbiemri,
            'email' => $request->email,
            'fjalekalimi_hash' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
        ]);

        return redirect()->route('stafi.index')->with('success', 'Anëtari i ri i stafit u shtua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stafi = User::findOrFail($id);
        return view('stafi.edit', ['staf' => $stafi]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stafi = User::findOrFail($id);
        
        $request->validate([
            'emri' => ['required', 'string', 'max:255'],
            'mbiemri' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:perdoruesit,email,'.$stafi->perdorues_id.',perdorues_id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'rol_id' => ['required', 'exists:rolet,rol_id'],
        ]);

        $stafi->emri = $request->emri;
        $stafi->mbiemri = $request->mbiemri;
        $stafi->email = $request->email;
        $stafi->rol_id = $request->rol_id;

        if ($request->filled('password')) {
            $stafi->fjalekalimi_hash = Hash::make($request->password);
        }

        $stafi->save();

        return redirect()->route('stafi.index')->with('success', 'Të dhënat u modifikuan me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stafi = User::findOrFail($id);
        
        if (auth()->id() == $stafi->perdorues_id) {
            return back()->with('error', 'Ju nuk mund të fshini llogarinë tuaj!');
        }

        // Kontrollo nëse përdoruesi është i caktuar në ndonjë projekt
        $projektCount = DB::table('projektet')
            ->where('mjeshtri_caktuar_id', $stafi->perdorues_id)
            ->orWhere('montatori_caktuar_id', $stafi->perdorues_id)
            ->count();
            
        if ($projektCount > 0) {
            return back()->with('error', 'Ky anëtar i stafit nuk mund të fshihet sepse është i caktuar në ' . $projektCount . ' projekt(e).');
        }

        $stafi->delete();

        return redirect()->route('stafi.index')->with('success', 'Anëtari i stafit u fshi me sukses.');
    }
}
