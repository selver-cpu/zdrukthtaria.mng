<?php

namespace App\Http\Controllers;

use App\Models\Njoftimet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NjoftimetController extends Controller
{
    /**
     * Shfaq listën e të gjitha njoftimeve për përdoruesin e kyçur.
     */
    public function index()
    {
        $njoftimet = Auth::user()->njoftimet()->latest('data_krijimit')->paginate(15);

        return view('njoftimet.index', compact('njoftimet'));
    }

    /**
     * Shëno një njoftim specifik si të lexuar.
     * 
     * @param int|Njoftimet $njoftim Njoftimi ose ID e njoftimit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($njoftim)
    {
        // Nëse parametri është një ID
        if (is_numeric($njoftim)) {
            $njoftim = Auth::user()->njoftimet()->find($njoftim);
            
            if ($njoftim) {
                $njoftim->update(['lexuar' => true]);
                
                if ($njoftim->projekt_id) {
                    return redirect()->route('projektet.show', $njoftim->projekt_id);
                }
            }
            
            return redirect()->route('njoftimet.index')->with('success', 'Njoftimi u shënua si i lexuar.');
        } 
        // Nëse parametri është një objekt Njoftimet
        else {
            // Ensure the user owns the notification
            if ($njoftim->perdorues_id !== Auth::id()) {
                abort(403, 'Ky veprim nuk është i autorizuar.');
            }

            // Mark as read if it's not already
            if (!$njoftim->lexuar) {
                $njoftim->update(['lexuar' => true]);
            }

            return redirect()->route('njoftimet.index');
        }
    }
    
    /**
     * Shëno të gjitha njoftimet si të lexuara.
     */
    public function markAllAsRead()
    {
        Auth::user()->njoftimet()->where('lexuar', false)->update(['lexuar' => true]);
        
        return back()->with('success', 'Të gjitha njoftimet u shënuan si të lexuara.');
    }

    /**
     * Display a listing of the user's notifications.
     */
    public function indexOld()
    {
        $njoftimet = Auth::user()->njoftimet()->latest('data_krijimit')->paginate(20);

        // Mark all notifications as read when the user visits the index page.
        Auth::user()->njoftimet()->where('lexuar', false)->update(['lexuar' => true]);

        return view('njoftimet.index', compact('njoftimet'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Njoftimet $njoftim)
    {
        // Ensure the user owns the notification
        if ($njoftim->perdorues_id !== Auth::id()) {
            abort(403, 'Ky veprim nuk është i autorizuar.');
        }

        $njoftim->delete();

        return back()->with('success', 'Njoftimi u fshi me sukses.');
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'perdorues_id' => 'required|exists:perdoruesit,perdorues_id',
            'projekt_id' => 'nullable|exists:projektet,projekt_id',
            'mesazhi' => 'required|string',
            'lloji_njoftimit' => 'required|in:email,sms,system'
        ]);

        $njoftim = Njoftimet::create([
            'perdorues_id' => $validated['perdorues_id'],
            'projekt_id' => $validated['projekt_id'],
            'mesazhi' => $validated['mesazhi'],
            'lloji_njoftimit' => $validated['lloji_njoftimit'],
            'lexuar' => false
        ]);

        // Dërgo eventin për të aktivizuar listener-in dhe broadcast
        event(new \App\Events\NjoftimIRi($njoftim));

        return response()->json([
            'success' => true,
            'message' => 'Njoftimi u krijua me sukses',
            'njoftim' => $njoftim
        ]);
    }
}
