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
     * Check admin access
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function checkAdminAccess()
    {
        try {
            // Check if user is logged in
            if (!auth()->check()) {
                return redirect()->route('dashboard')->with('error', 'Ju lutem kyçuni për të vazhduar.');
            }
            
            // Direct role ID check for admin (rol_id = 1)
            if (auth()->user()->rol_id == 1) {
                return null; // Allow access
            }
            
            // Fallback to hasRole if available
            if (method_exists(auth()->user(), 'hasRole')) {
                // Try/catch to handle any potential issues with the hasRole method
                try {
                    if (auth()->user()->hasRole('administrator') || auth()->user()->hasRole('admin')) {
                        return null; // Allow access
                    }
                } catch (\Exception $e) {
                    // Log error but continue with the fallback check
                }
            }
            
            // If we get here, user doesn't have access
            return redirect()->route('dashboard')->with('error', 'Vetëm administratori mund të menaxhojë stafin.');
        } catch (\Exception $e) {
            // Catch any other errors and redirect to dashboard
            return redirect()->route('dashboard')->with('error', 'Ndodhi një gabim. Ju lutem provoni përsëri.');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check admin access
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }
        
        try {
            // Use a direct DB query to avoid any potential model relationship issues
            $stafi = DB::table('perdoruesit')
                ->select('perdoruesit.*', 'rolet.emri_rolit')
                ->leftJoin('rolet', 'perdoruesit.rol_id', '=', 'rolet.rol_id')
                ->orderBy('perdoruesit.emri')
                ->paginate(15);
                
            return view('stafi.index', compact('stafi'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error loading staff: ' . $e->getMessage());
            
            // Return an error message
            return redirect()->route('dashboard')
                ->with('error', 'Ndodhi një gabim gjatë ngarkimit të stafit. Ju lutem provoni përsëri.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check admin access
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }
        
        return view('stafi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check admin access
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }
        
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
        // Check admin access
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }
        
        $stafi = User::findOrFail($id);
        return view('stafi.edit', ['staf' => $stafi]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Check admin access
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }
        
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
        // Check admin access
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }
        
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
