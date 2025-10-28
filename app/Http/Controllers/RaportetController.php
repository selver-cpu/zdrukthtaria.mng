<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projektet;
use App\Models\Klientet;
use App\Models\User;
use App\Models\StatusetProjektit;
use App\Models\Materialet;
use App\Models\ProjektMateriale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RaportetController extends Controller
{
    /**
     * Kontrollon nëse përdoruesi ka të drejtë të eksportojë raporte
     */
    protected function checkReportExportAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Ju lutem kyçuni për të vazhduar.');
        }
        
        // Vetëm Admin (1), Menaxher (2) dhe Disajnere (5) mund të eksportojnë raporte
        if (!in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('raportet.index')
                ->with('error', 'Nuk keni të drejtë të eksportoni raporte.');
        }
        
        return null;
    }
    /**
     * Display the main reports dashboard
     */
    public function index()
    {
        // Statistika të përgjithshme
        $totalProjekte = Projektet::count();
        $projekteAktive = Projektet::whereHas('statusi_projektit', function($query) {
            $query->where('emri_statusit', 'Në progres');
        })->count();
        
        $projektePerfunduar = Projektet::whereHas('statusi_projektit', function($query) {
            $query->where('emri_statusit', 'Përfunduar');
        })->count();
        
        $totalKliente = Klientet::count();
        
        // Numri i mjeshtrave dhe montuesve
        $mjeshtrat = User::whereHas('rol', function($q) {
            $q->where('emri_rolit', 'mjeshtër');
        })->count();
        
        $montuesit = User::whereHas('rol', function($q) {
            $q->where('emri_rolit', 'montues');
        })->count();
        
        // Projekte sipas statusit
        $projektePerStatus = Projektet::select('projektet.status_id', 'statuset_projektit.emri_statusit', DB::raw('count(*) as total'))
            ->leftJoin('statuset_projektit', 'projektet.status_id', '=', 'statuset_projektit.status_id')
            ->groupBy('projektet.status_id', 'statuset_projektit.emri_statusit')
            ->get()
            ->map(function($item) {
                return (object)[
                    'status' => (object)[
                        'emri_statusit' => $item->status_id ? ($item->emri_statusit ?? 'Pa Status') : 'Pa Status'
                    ],
                    'total' => $item->total
                ];
            });
        
        // Projekte të krijuara këtë muaj
        $projekteMuajAktual = Projektet::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Top 5 klientët me më shumë projekte
        $topKliente = Klientet::withCount('projektet')
            ->orderBy('projektet_count', 'desc')
            ->take(5)
            ->get();

        return view('raportet.index', compact(
            'totalProjekte', 'projekteAktive', 'projektePerfunduar', 
            'totalKliente', 'mjeshtrat', 'montuesit', 'projektePerStatus', 
            'projekteMuajAktual', 'topKliente'
        ));
    }

    /**
     * Raporti i projekteve
     */
    public function projektet(Request $request)
    {
        $query = Projektet::with(['klient', 'statusi_projektit', 'mjeshtri', 'montuesi']);
        
        // Filtrimi sipas datës
        if ($request->filled('data_nga')) {
            $query->where('created_at', '>=', $request->data_nga);
        }
        
        if ($request->filled('data_deri')) {
            $query->where('created_at', '<=', $request->data_deri);
        }
        
        // Filtrimi sipas statusit
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        
        // Filtrimi sipas klientit
        if ($request->filled('klient_id')) {
            $query->where('klient_id', $request->klient_id);
        }
        
        $projektet = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuset = StatusetProjektit::all();
        $klientet = Klientet::all();
        
        return view('raportet.projektet', compact('projektet', 'statuset', 'klientet'));
    }

    /**
     * Raporti i materialeve
     */
    public function materialet(Request $request)
    {
        $query = ProjektMateriale::with(['material', 'projekt'])
            ->select('material_id', DB::raw('SUM(sasia_perdorur) as total_perdorur'))
            ->groupBy('material_id');
        
        // Filtrimi sipas datës së projektit
        if ($request->filled('data_nga') || $request->filled('data_deri')) {
            $query->whereHas('projekt', function($q) use ($request) {
                if ($request->filled('data_nga')) {
                    $q->where('created_at', '>=', $request->data_nga);
                }
                if ($request->filled('data_deri')) {
                    $q->where('created_at', '<=', $request->data_deri);
                }
            });
        }
        
        $materialetPerdorur = $query->orderBy('total_perdorur', 'desc')->get();
        
        // Lista e të gjitha materialeve për dropdown
        $materialet = Materialet::all();
        
        return view('raportet.materialet', compact('materialetPerdorur', 'materialet'));
    }

    /**
     * Raporti i performancës së stafit
     */
    public function stafi(Request $request)
    {
        // Statistika për mjeshtrat
        $mjeshtrat = User::whereHas('role', function($query) {
            $query->where('emri_rolit', 'mjeshtër');
        })->withCount([
            'projekteSiMjesher',
            'projekteSiMjesher as projekte_perfunduar' => function($query) {
                $query->whereHas('statusi_projektit', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }
        ])->get();
        
        // Statistika për montuesit
        $montuesit = User::whereHas('role', function($query) {
            $query->where('emri_rolit', 'montues');
        })->withCount([
            'projekteSiMontues',
            'projekteSiMontues as projekte_perfunduar' => function($query) {
                $query->whereHas('statusi_projektit', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }
        ])->get();
        
        return view('raportet.stafi', compact('mjeshtrat', 'montuesit'));
    }

    /**
     * Eksportimi i raporteve në PDF
     */
    public function eksporto(Request $request)
    {
        // Kontrollo qasjen për eksportim
        if ($redirect = $this->checkReportExportAccess()) {
            return $redirect;
        }
        
        switch($request->lloji_raportit) {
            case 'projektet':
                return $this->eksportoProjektet($request);
            case 'materialet':
                return $this->eksportoMaterialet($request);
            case 'stafi':
                return $this->eksportoStafi($request);
            default:
                abort(404, 'Lloji i raportit nuk u gjet');
        }
    }

    private function eksportoProjektet(Request $request)
    {
        // Merr të dhënat e projekteve me filtrat e aplikuara
        $query = Projektet::with(['klient', 'statusi_projektit', 'mjeshtri', 'montuesi']);

        if ($request->filled('data_nga')) {
            $query->where('created_at', '>=', $request->data_nga);
        }

        if ($request->filled('data_deri')) {
            $query->where('created_at', '<=', $request->data_deri);
        }

        if ($request->filled('klient_id')) {
            $query->where('klient_id', $request->klient_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $projektet = $query->get();

        // Krijo PDF me DomPDF (mund të instalosh me: composer require barryvdh/laravel-dompdf)
        $html = view('raportet.pdf.projektet', compact('projektet'))->render();
        
        // Për tani, kthe një përgjigje të thjeshtë
        return response()->json([
            'message' => 'Eksportimi i raportit të projekteve do të implementohet së shpejti.',
            'data_count' => $projektet->count()
        ]);
    }

    private function eksportoMaterialet(Request $request)
    {
        // Merr të dhënat e materialeve me filtrat e aplikuara
        $query = ProjektMateriale::with(['material', 'projekt'])
            ->select('material_id', 
                DB::raw('SUM(sasia) as total_perdorur'))
            ->groupBy('material_id');

        if ($request->filled('data_nga') || $request->filled('data_deri')) {
            $query->whereHas('projekt', function($q) use ($request) {
                if ($request->filled('data_nga')) {
                    $q->where('created_at', '>=', $request->data_nga);
                }
                if ($request->filled('data_deri')) {
                    $q->where('created_at', '<=', $request->data_deri);
                }
            });
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        $materialetPerdorur = $query->get();

        return response()->json([
            'message' => 'Eksportimi i raportit të materialeve do të implementohet së shpejti.',
            'data_count' => $materialetPerdorur->count()
        ]);
    }

    private function eksportoStafi(Request $request)
    {
        // Merr të dhënat e stafit
        $mjeshtrat = User::whereHas('role', function($query) {
                $query->where('emri_rolit', 'mjeshtër');
            })
            ->withCount(['projekteSiMjesher', 'projekteSiMjesher as projekte_perfunduar' => function($query) {
                $query->whereHas('statusi_projektit', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }])
            ->get();

        $montuesit = User::whereHas('roli', function($query) {
                $query->where('emri_rolit', 'montues');
            })
            ->withCount(['projekteSiMontues', 'projekteSiMontues as projekte_perfunduar' => function($query) {
                $query->whereHas('statusi_projektit', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }])
            ->get();

        return response()->json([
            'message' => 'Eksportimi i raportit të stafit do të implementohet së shpejti.',
            'mjeshtrat_count' => $mjeshtrat->count(),
            'montuesit_count' => $montuesit->count()
        ]);
    }

    /**
     * Raporti Financiar - I aksesueshëm vetëm nga administratorët
     */
    public function financiar(Request $request)
    {
        // Kontrollo nëse përdoruesi është administrator
        $user = auth()->user();
        if (!$user || $user->rol->emri_rolit !== 'administrator') {
            abort(403, 'Kjo faqe është e aksesueshme vetëm për administratorët.');
        }

        // Merr të gjitha projektet me të dhënat e nevojshme financiare
        $query = Projektet::with(['klient', 'statusi_projektit', 'materialet', 'proceset']);
        
        // Filtro sipas datës
        if ($request->filled('data_nga')) {
            $query->where('created_at', '>=', $request->data_nga);
        }
        
        if ($request->filled('data_deri')) {
            $query->where('created_at', '<=', $request->data_deri);
        }
        
        // Filtro sipas klientit
        if ($request->filled('klient_id')) {
            $query->where('klient_id', $request->klient_id);
        }
        
        // Filtro sipas statusit
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        
        $projektet = $query->orderBy('created_at', 'desc')->get();
        
        // Llogarit totalet financiare
        $totaliAktive = 0;
        $totaliTeArdhura = 0;
        $totaliShpenzime = 0;
        $totaliFitimi = 0;
        
        foreach ($projektet as $projekt) {
            // Llogarit shpenzimet nga materialet
            $shpenzimeMateriale = $projekt->materialet->sum('pivot.shuma');
            
            // Merr buxhetin e projektit (0 nëse nuk është vendosur)
            $teArdhura = $projekt->buxheti ?? 0;
            
            // Llogarit fitimin
            $fitim = $teArdhura - $shpenzimeMateriale;
            
            // Shto në totalet
            $totaliTeArdhura += $teArdhura;
            $totaliShpenzime += $shpenzimeMateriale;
            $totaliFitimi += $fitim;
            
            // Shto në totalin e projekteve aktive nëse është në progres
            if ($projekt->statusi_projektit && $projekt->statusi_projektit->emri_statusit == 'Në progres') {
                $totaliAktive += $teArdhura;
            }
        }
        
        // Merr të gjithë klientët për dropdown
        $klientet = Klientet::all();
        $statuset = StatusetProjektit::all();
        
        // Numri i projekteve të faturuara (projekte me status 'Përfunduar' ose 'Faturuar')
        $projekteTeFaturuara = $projektet->filter(function($projekt) {
            return $projekt->statusi_projektit && 
                   in_array($projekt->statusi_projektit->emri_statusit, ['Përfunduar', 'Faturuar']);
        })->count();
        
        // Merr 5 klientët më të mëdhenj sipas vlerës së projekteve
        $topKlientet = Klientet::withCount(['projektet as projektet_count' => function($query) {
            $query->select(DB::raw('count(*) as projektet_count'));
        }])
        ->withSum('projektet as totali_projekteve', 'buxheti')
        ->orderBy('totali_projekteve', 'desc')
        ->take(5)
        ->get()
        ->each(function ($klient) {
            $klient->projekti_me_i_madh = $klient->projektet->sortByDesc('buxheti')->first();
            $klient->vlera_mesatare = $klient->projektet_count > 0 
                ? $klient->totali_projekteve / $klient->projektet_count 
                : 0;
        });
        
        // Krijon një array me të dhënat për grafikun e trendit mujor
        $muajt = [];
        $teArdhuratMujore = [];
        $shpenzimetMujore = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $muaji = $data->format('M Y');
            $muajt[] = $muaji;
            
            // Llogarit të ardhurat dhe shpenzimet për këtë muaj
            $teArdhura = Projektet::whereYear('created_at', $data->year)
                ->whereMonth('created_at', $data->month)
                ->sum('buxheti');
                
            $shpenzime = 0;
            $projektetMuaji = Projektet::whereYear('created_at', $data->year)
                ->whereMonth('created_at', $data->month)
                ->with('materialet')
                ->get();
                
            foreach ($projektetMuaji as $projekt) {
                $shpenzime += $projekt->materialet->sum('pivot.shuma');
            }
            
            $teArdhuratMujore[] = $teArdhura;
            $shpenzimetMujore[] = $shpenzime;
        }
        
        return view('raportet.financiar', compact(
            'projektet', 
            'klientet', 
            'statuset',
            'totaliAktive',
            'totaliTeArdhura',
            'totaliShpenzime',
            'totaliFitimi',
            'muajt',
            'teArdhuratMujore',
            'shpenzimetMujore',
            'projekteTeFaturuara',
            'topKlientet'
        ));
    }
}
