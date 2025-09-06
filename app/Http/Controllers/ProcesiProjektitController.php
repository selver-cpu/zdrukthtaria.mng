<?php

namespace App\Http\Controllers;

use App\Models\Projektet;
use App\Models\ProcesiProjektit;
use App\Models\StatusetProjektit;
use App\Models\Njoftimet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcesiProjektitController extends Controller
{
    /**
     * Shfaq historikun e procesit për një projekt specifik.
     */
    public function index(Projektet $projekt)
    {
        $proceset = $projekt->proceset()
            ->with(['perdoruesi', 'statusi_projektit'])
            ->orderBy('data_ndryshimit', 'desc')
            ->get();

        return view('procesi.index', compact('projekt', 'proceset'));
    }

    /**
     * Regjistro një ndryshim në procesin e projektit.
     */
    public function store(Request $request, Projektet $projekt)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:statuset_projektit,status_id',
            'komente' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Krijo rekordin e procesit
            $proces = ProcesiProjektit::create([
                'projekt_id' => $projekt->projekt_id,
                'status_id' => $validated['status_id'],
                'perdorues_id' => Auth::id(),
                'data_ndryshimit' => now(),
                'komente' => $validated['komente']
            ]);

            // Përditëso statusin e projektit
            $projekt->update(['status_id' => $validated['status_id']]);

            // Krijo njoftime për stafin e përfshirë
            $this->createNotifications($projekt, $validated['status_id']);

            DB::commit();
            return back()->with('success', 'Procesi u regjistrua me sukses.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ndodhi një gabim gjatë regjistrimit të procesit.');
        }
    }

    /**
     * Krijo njoftime për stafin e përfshirë në projekt.
     */
    private function createNotifications(Projektet $projekt, $status_id)
    {
        $status = StatusetProjektit::find($status_id);
        $message = "Projekti '{$projekt->emri_projektit}' ka kaluar në statusin: {$status->emri_statusit}";

        // Njofto mjeshtrin
        if ($projekt->mjeshtri_caktuar_id) {
            Njoftimet::create([
                'perdorues_id' => $projekt->mjeshtri_caktuar_id,
                'projekt_id' => $projekt->projekt_id,
                'lloji_njoftimit' => 'system',
                'mesazhi' => $message,
                'data_krijimit' => now(),
                'lexuar' => false
            ]);
        }

        // Njofto montuesin
        if ($projekt->montuesi_caktuar_id) {
            Njoftimet::create([
                'perdorues_id' => $projekt->montuesi_caktuar_id,
                'projekt_id' => $projekt->projekt_id,
                'lloji_njoftimit' => 'system',
                'mesazhi' => $message,
                'data_krijimit' => now(),
                'lexuar' => false
            ]);
        }
    }

    /**
     * Shfaq detajet e një procesi specifik.
     */
    public function show(Projektet $projekt, ProcesiProjektit $proces)
    {
        return view('procesi.show', compact('projekt', 'proces'));
    }
}
