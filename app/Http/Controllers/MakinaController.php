<?php

namespace App\Http\Controllers;

use App\Models\Makina;
use App\Models\MirembajtjeEvidenca;
use App\Models\MirembajtjePlan;
use Illuminate\Http\Request;

class MakinaController extends Controller
{
    protected function checkAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Ju lutem kyçuni për të vazhduar.');
        }

        // Admin (1) dhe Menaxher (2) menaxhojnë makinat; të tjerët vetëm shohin
        return null;
    }

    protected function canManage(): bool
    {
        return auth()->check() && in_array(auth()->user()->rol_id, [1, 2]);
    }

    public function index(Request $request)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $query = Makina::withCount([
            'planetAktive',
            'planetVonuar',
        ])->orderBy('emri');

        if ($request->filled('lloji')) {
            $query->where('lloji', $request->lloji);
        }
        if ($request->filled('statusi')) {
            $query->where('statusi', $request->statusi);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('emri', 'like', "%{$q}%")
                    ->orWhere('marka', 'like', "%{$q}%")
                    ->orWhere('modeli', 'like', "%{$q}%")
                    ->orWhere('numri_serik', 'like', "%{$q}%");
            });
        }

        $makinat = $query->paginate(15)->withQueryString();

        $stats = [
            'totale' => Makina::count(),
            'aktive' => Makina::where('statusi', 'aktive')->count(),
            'vonuara' => MirembajtjePlan::where('aktiv', true)
                ->whereNotNull('data_radhese')
                ->whereDate('data_radhese', '<', now()->toDateString())
                ->count(),
            'te_hapura' => MirembajtjeEvidenca::whereIn('statusi', ['e_hapur', 'ne_progres'])->count(),
        ];

        return view('makinat.index', [
            'makinat' => $makinat,
            'stats' => $stats,
            'llojet' => Makina::LLOJET,
            'statuset' => Makina::STATUSET,
            'canManage' => $this->canManage(),
        ]);
    }

    public function create()
    {
        if (!$this->canManage()) {
            return redirect()->route('makinat.index')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të shtojnë makina.');
        }

        return view('makinat.create', [
            'llojet' => Makina::LLOJET,
            'statuset' => Makina::STATUSET,
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->canManage()) {
            return redirect()->route('makinat.index')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të shtojnë makina.');
        }

        $data = $request->validate([
            'emri' => 'required|string|max:255',
            'lloji' => 'required|in:' . implode(',', array_keys(Makina::LLOJET)),
            'marka' => 'nullable|string|max:100',
            'modeli' => 'nullable|string|max:100',
            'numri_serik' => 'nullable|string|max:100',
            'lokacioni' => 'nullable|string|max:255',
            'statusi' => 'required|in:' . implode(',', array_keys(Makina::STATUSET)),
            'kritikaliteti' => 'required|in:A,B,C',
            'data_blerjes' => 'nullable|date',
            'ore_pune' => 'nullable|numeric|min:0',
            'shenime' => 'nullable|string',
        ]);

        $data['ore_pune'] = $data['ore_pune'] ?? 0;

        $makina = Makina::create($data);

        return redirect()->route('makinat.show', $makina)
            ->with('success', 'Makina u shtua me sukses. Shto planet e mirëmbajtjes.');
    }

    public function show(Makina $makina)
    {
        if ($redirect = $this->checkAccess()) {
            return $redirect;
        }

        $makina->load([
            'planet' => fn ($q) => $q->orderBy('data_radhese'),
            'evidenca' => fn ($q) => $q->with('perdoruesi')->latest('data_krijimit')->limit(20),
        ]);

        return view('makinat.show', [
            'makina' => $makina,
            'canManage' => $this->canManage(),
            'frekuencat' => MirembajtjePlan::FREKUENCAT,
            'llojetEvidenca' => MirembajtjeEvidenca::LLOJET,
            'statusetEvidenca' => MirembajtjeEvidenca::STATUSET,
            'prioritetet' => MirembajtjeEvidenca::PRIORITETET,
        ]);
    }

    public function edit(Makina $makina)
    {
        if (!$this->canManage()) {
            return redirect()->route('makinat.index')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të modifikojnë makinat.');
        }

        return view('makinat.edit', [
            'makina' => $makina,
            'llojet' => Makina::LLOJET,
            'statuset' => Makina::STATUSET,
        ]);
    }

    public function update(Request $request, Makina $makina)
    {
        if (!$this->canManage()) {
            return redirect()->route('makinat.index')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të modifikojnë makinat.');
        }

        $data = $request->validate([
            'emri' => 'required|string|max:255',
            'lloji' => 'required|in:' . implode(',', array_keys(Makina::LLOJET)),
            'marka' => 'nullable|string|max:100',
            'modeli' => 'nullable|string|max:100',
            'numri_serik' => 'nullable|string|max:100',
            'lokacioni' => 'nullable|string|max:255',
            'statusi' => 'required|in:' . implode(',', array_keys(Makina::STATUSET)),
            'kritikaliteti' => 'required|in:A,B,C',
            'data_blerjes' => 'nullable|date',
            'ore_pune' => 'nullable|numeric|min:0',
            'shenime' => 'nullable|string',
        ]);

        $makina->update($data);

        return redirect()->route('makinat.show', $makina)
            ->with('success', 'Makina u përditësua me sukses.');
    }

    public function destroy(Makina $makina)
    {
        if (!$this->canManage()) {
            return redirect()->route('makinat.index')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të fshijnë makinat.');
        }

        $makina->delete();

        return redirect()->route('makinat.index')
            ->with('success', 'Makina u fshi me sukses.');
    }
}
