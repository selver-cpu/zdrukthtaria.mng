<?php

namespace App\Http\Controllers;

use App\Models\Makina;
use App\Models\MirembajtjeEvidenca;
use App\Models\MirembajtjePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MirembajtjeController extends Controller
{
    protected function canManage(): bool
    {
        return auth()->check() && in_array(auth()->user()->rol_id, [1, 2, 3]); // Admin, Menaxher, Mjeshtër
    }

    public function dashboard()
    {
        $planetVonuar = MirembajtjePlan::with('makina')
            ->where('aktiv', true)
            ->whereNotNull('data_radhese')
            ->whereDate('data_radhese', '<', now()->toDateString())
            ->orderBy('data_radhese')
            ->get();

        $planetSot = MirembajtjePlan::with('makina')
            ->where('aktiv', true)
            ->whereDate('data_radhese', now()->toDateString())
            ->orderBy('titulli')
            ->get();

        $planetSjavore = MirembajtjePlan::with('makina')
            ->where('aktiv', true)
            ->whereDate('data_radhese', '>', now()->toDateString())
            ->whereDate('data_radhese', '<=', now()->addDays(7)->toDateString())
            ->orderBy('data_radhese')
            ->get();

        $puneTeHapura = MirembajtjeEvidenca::with(['makina', 'perdoruesi'])
            ->whereIn('statusi', ['e_hapur', 'ne_progres'])
            ->orderByRaw("CASE prioriteti WHEN 'urgjent' THEN 1 WHEN 'larte' THEN 2 WHEN 'mesatar' THEN 3 ELSE 4 END")
            ->latest('data_krijimit')
            ->limit(15)
            ->get();

        return view('makinat.dashboard', [
            'planetVonuar' => $planetVonuar,
            'planetSot' => $planetSot,
            'planetSjavore' => $planetSjavore,
            'puneTeHapura' => $puneTeHapura,
            'canManage' => $this->canManage(),
        ]);
    }

    public function storePlan(Request $request, Makina $makina)
    {
        if (!$this->canManage()) {
            return back()->with('error', 'Nuk keni të drejtë të shtoni plane mirëmbajtjeje.');
        }

        $data = $request->validate([
            'titulli' => 'required|string|max:255',
            'checklist' => 'nullable|string',
            'frekuenca' => 'required|in:' . implode(',', array_keys(MirembajtjePlan::FREKUENCAT)),
            'intervali' => 'required|integer|min:1|max:365',
            'data_radhese' => 'nullable|date',
            'aktiv' => 'nullable|boolean',
        ]);

        $data['makin_id'] = $makina->makin_id;
        $data['aktiv'] = $request->boolean('aktiv', true);
        $data['intervali'] = $data['intervali'] ?? 1;

        if (empty($data['data_radhese'])) {
            $data['data_radhese'] = now()->toDateString();
        }

        MirembajtjePlan::create($data);

        return back()->with('success', 'Plani i mirëmbajtjes u shtua.');
    }

    public function updatePlan(Request $request, MirembajtjePlan $plan)
    {
        if (!$this->canManage()) {
            return back()->with('error', 'Nuk keni të drejtë të modifikoni planet.');
        }

        $data = $request->validate([
            'titulli' => 'required|string|max:255',
            'checklist' => 'nullable|string',
            'frekuenca' => 'required|in:' . implode(',', array_keys(MirembajtjePlan::FREKUENCAT)),
            'intervali' => 'required|integer|min:1|max:365',
            'data_radhese' => 'nullable|date',
            'aktiv' => 'nullable|boolean',
        ]);

        $data['aktiv'] = $request->boolean('aktiv', true);
        $plan->update($data);

        return back()->with('success', 'Plani u përditësua.');
    }

    public function destroyPlan(MirembajtjePlan $plan)
    {
        if (!$this->canManage()) {
            return back()->with('error', 'Nuk keni të drejtë të fshini planet.');
        }

        $plan->delete();

        return back()->with('success', 'Plani u fshi.');
    }

    public function storeEvidenca(Request $request, Makina $makina)
    {
        if (!$this->canManage()) {
            return back()->with('error', 'Nuk keni të drejtë të regjistroni mirëmbajtje.');
        }

        $data = $request->validate([
            'plan_id' => 'nullable|exists:mirembajtje_planet,plan_id',
            'lloji' => 'required|in:' . implode(',', array_keys(MirembajtjeEvidenca::LLOJET)),
            'titulli' => 'required|string|max:255',
            'pershkrimi' => 'nullable|string',
            'statusi' => 'required|in:' . implode(',', array_keys(MirembajtjeEvidenca::STATUSET)),
            'prioriteti' => 'required|in:' . implode(',', array_keys(MirembajtjeEvidenca::PRIORITETET)),
            'data_planifikuar' => 'nullable|date',
            'data_kryerjes' => 'nullable|date',
            'kohezgjatja_minuta' => 'nullable|integer|min:0',
            'kosto' => 'nullable|numeric|min:0',
            'pjeset_e_perdorura' => 'nullable|string',
            'gjetjet' => 'nullable|string',
            'ore_pune' => 'nullable|numeric|min:0',
        ]);

        $data['makin_id'] = $makina->makin_id;
        $data['perdorues_id'] = auth()->user()->perdorues_id;

        if ($data['statusi'] === 'perfunduar' && empty($data['data_kryerjes'])) {
            $data['data_kryerjes'] = now();
        }

        $evidenca = MirembajtjeEvidenca::create($data);

        // Update hour meter if provided
        if ($request->filled('ore_pune')) {
            $makina->update(['ore_pune' => $request->ore_pune]);
        }

        // Advance PM schedule when preventive work is completed from a plan
        if ($evidenca->statusi === 'perfunduar' && $evidenca->plan_id) {
            $plan = MirembajtjePlan::find($evidenca->plan_id);
            if ($plan) {
                $plan->shenoSiTeKryer(
                    $evidenca->data_kryerjes ? Carbon::parse($evidenca->data_kryerjes) : now(),
                    $request->filled('ore_pune') ? (float) $request->ore_pune : null
                );
            }
        }

        return back()->with('success', 'Evidenca e mirëmbajtjes u regjistrua.');
    }

    public function completePlan(Request $request, MirembajtjePlan $plan)
    {
        if (!$this->canManage()) {
            return back()->with('error', 'Nuk keni të drejtë të shënoni mirëmbajtjen si të kryer.');
        }

        $data = $request->validate([
            'gjetjet' => 'nullable|string',
            'kohezgjatja_minuta' => 'nullable|integer|min:0',
            'kosto' => 'nullable|numeric|min:0',
            'pjeset_e_perdorura' => 'nullable|string',
            'ore_pune' => 'nullable|numeric|min:0',
        ]);

        $makina = $plan->makina;

        if ($request->filled('ore_pune')) {
            $makina->update(['ore_pune' => $request->ore_pune]);
        }

        MirembajtjeEvidenca::create([
            'makin_id' => $makina->makin_id,
            'plan_id' => $plan->plan_id,
            'lloji' => 'preventive',
            'titulli' => $plan->titulli,
            'pershkrimi' => $plan->checklist,
            'statusi' => 'perfunduar',
            'prioriteti' => 'mesatar',
            'perdorues_id' => auth()->user()->perdorues_id,
            'data_planifikuar' => $plan->data_radhese,
            'data_kryerjes' => now(),
            'kohezgjatja_minuta' => $data['kohezgjatja_minuta'] ?? null,
            'kosto' => $data['kosto'] ?? null,
            'pjeset_e_perdorura' => $data['pjeset_e_perdorura'] ?? null,
            'gjetjet' => $data['gjetjet'] ?? null,
        ]);

        $plan->shenoSiTeKryer(now(), $request->filled('ore_pune') ? (float) $request->ore_pune : null);

        return back()->with('success', 'Mirëmbajtja u shënua si e kryer. Data e radhës u përditësua.');
    }

    public function updateEvidenca(Request $request, MirembajtjeEvidenca $evidenca)
    {
        if (!$this->canManage()) {
            return back()->with('error', 'Nuk keni të drejtë të përditësoni evidencën.');
        }

        $data = $request->validate([
            'statusi' => 'required|in:' . implode(',', array_keys(MirembajtjeEvidenca::STATUSET)),
            'prioriteti' => 'nullable|in:' . implode(',', array_keys(MirembajtjeEvidenca::PRIORITETET)),
            'gjetjet' => 'nullable|string',
            'kohezgjatja_minuta' => 'nullable|integer|min:0',
            'kosto' => 'nullable|numeric|min:0',
            'pjeset_e_perdorura' => 'nullable|string',
        ]);

        $wasOpen = $evidenca->statusi !== 'perfunduar';

        if ($data['statusi'] === 'perfunduar' && !$evidenca->data_kryerjes) {
            $data['data_kryerjes'] = now();
        }

        $evidenca->update($data);

        if ($wasOpen && $evidenca->statusi === 'perfunduar' && $evidenca->plan_id) {
            $plan = $evidenca->plan;
            if ($plan) {
                $plan->shenoSiTeKryer(
                    $evidenca->data_kryerjes ? Carbon::parse($evidenca->data_kryerjes) : now()
                );
            }
        }

        return back()->with('success', 'Statusi i evidencës u përditësua.');
    }
}
