<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projektet;
use App\Models\Klientet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with key statistics based on user role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [];
        $now = Carbon::now();

        // Initialize default stats array
        $stats = [
            'projektet_aktive' => 0,
            'projektet_muaji' => 0,
            'kliente_aktive' => 0,
            'projektet_mjeshtrit' => 0,
            'projektet_perfunduara_muaji' => 0,
            'detyrat_sotme' => 0,
            'montimet_aktive' => 0,
            'montimet_perfunduara_muaji' => 0,
            'montimet_sotme' => 0,
            'projektet_fundit' => collect()
        ];

        // Kontrollo nëse përdoruesi ka rol
        if (!$user->rol) {
            return view('dashboard', [
                'stats' => $stats,
                'role' => 'undefined',
                'projektet_e_fundit' => collect(),
                'projektet_e_mia' => collect(),
                'detyrat_e_ardhshme' => collect()
            ]);
        }
        
        // Ensure role name is properly set
        $role = $user->rol->emri_rolit ?? 'undefined';

        // Statistikat bazë për të gjithë përdoruesit
        $projektet_e_fundit = Projektet::with(['klient', 'statusi_projektit'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // Merr projektet specifike për përdoruesin bazuar në rolin
        $projektet_e_mia = collect();
        $detyrat_e_ardhshme = collect();
        if ($user->rol->emri_rolit === 'mjeshtër') {
            $projektet_e_mia = Projektet::with(['klient', 'statusi_projektit', 'fazat'])
                ->where('mjeshtri_caktuar_id', $user->perdorues_id)
                ->latest()
                ->get();
        } elseif ($user->rol->emri_rolit === 'montues') {
            $projektet_e_mia = Projektet::with(['klient', 'statusi_projektit', 'fazat'])
                ->where('montuesi_caktuar_id', $user->perdorues_id)
                ->latest()
                ->get();
        } elseif (in_array($user->rol->emri_rolit, ['administrator', 'menaxher'])) {
            $projektet_e_mia = Projektet::with(['klient', 'statusi_projektit', 'fazat'])
                ->latest()
                ->get();
        }

        if ($user->rol->emri_rolit === 'administrator' || $user->rol->emri_rolit === 'menaxher') {
            // Statistikat për administratorët dhe menaxherët
            $stats['projektet_aktive'] = Projektet::whereHas('statusi_projektit', function ($query) {
                $query->where('emri_statusit', 'Në Proces');
            })->orWhereDoesntHave('statusi_projektit')->count();

            $stats['projektet_muaji'] = Projektet::whereNotNull('created_at')
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count();

            $stats['kliente_aktive'] = Klientet::whereHas('projektet', function ($query) {
                $query->whereHas('statusi_projektit', function ($q) {
                    $q->where('emri_statusit', 'Në Proces');
                });
            })->count();

        } elseif ($user->rol->emri_rolit === 'mjeshtër') {
            // Statistikat për mjeshtrat
            $stats['projektet_mjeshtrit'] = Projektet::where('mjeshtri_caktuar_id', $user->perdorues_id)
                ->whereHas('statusi_projektit', function ($query) {
                    $query->where('emri_statusit', 'Në Proces');
                })->count();

            $stats['projektet_perfunduara_muaji'] = Projektet::where('mjeshtri_caktuar_id', $user->perdorues_id)
                ->whereHas('statusi_projektit', function ($query) {
                    $query->where('emri_statusit', 'Përfunduar');
                })
                ->whereNotNull('updated_at')
                ->whereMonth('updated_at', $now->month)
                ->whereYear('updated_at', $now->year)
                ->count();

            $stats['detyrat_sotme'] = Projektet::where('mjeshtri_caktuar_id', $user->perdorues_id)
                ->whereDate('data_fillimit_parashikuar', '<=', $now)
                ->whereDate('data_perfundimit_parashikuar', '>=', $now)
                ->count();

            // Filtro projektet e fundit për mjeshtrin
            $stats['projektet_fundit'] = Projektet::with(['klient', 'statusi_projektit'])
                ->where('mjeshtri_caktuar_id', $user->perdorues_id)
                ->latest()
                ->take(5)
                ->get();

        } elseif ($user->rol->emri_rolit === 'montues') {
            // Statistikat për montuesit
            $stats['montimet_aktive'] = Projektet::where('montuesi_caktuar_id', $user->perdorues_id)
                ->whereHas('statusi_projektit', function ($query) {
                    $query->where('emri_statusit', 'Në Proces');
                })->count();

            $stats['montimet_perfunduara_muaji'] = Projektet::where('montuesi_caktuar_id', $user->perdorues_id)
                ->whereHas('statusi_projektit', function ($query) {
                    $query->where('emri_statusit', 'Përfunduar');
                })
                ->whereMonth('updated_at', $now->month)
                ->whereYear('updated_at', $now->year)
                ->count();

            $stats['montimet_sotme'] = Projektet::where('montuesi_caktuar_id', $user->perdorues_id)
                ->whereDate('data_fillimit_parashikuar', '<=', $now)
                ->whereDate('data_perfundimit_parashikuar', '>=', $now)
                ->count();

            // Filtro projektet e fundit për montuesin
            $stats['projektet_fundit'] = Projektet::with(['klient', 'statusi_projektit'])
                ->where('montuesi_caktuar_id', $user->perdorues_id)
                ->latest()
                ->take(5)
                ->get();
        }

        // Ensure all collections are properly initialized
        $projektet_e_fundit = $projektet_e_fundit ?? collect();
        $projektet_e_mia = $projektet_e_mia ?? collect();
        
        // Prepare detyrat_e_ardhshme if not already set
        if (!isset($detyrat_e_ardhshme)) {
            $detyrat_e_ardhshme = collect();
            
            // For mjeshtër and montues, get upcoming tasks from their projects
            if (in_array($role, ['mjeshtër', 'montues'])) {
                $field = $role === 'mjeshtër' ? 'mjeshtri_caktuar_id' : 'montuesi_caktuar_id';
                
                $detyrat_e_ardhshme = Projektet::with(['klient', 'statusi_projektit'])
                    ->where($field, $user->perdorues_id)
                    ->whereDate('data_perfundimit_parashikuar', '>=', $now)
                    ->orderBy('data_perfundimit_parashikuar')
                    ->limit(10)
                    ->get();
            }
        }

        return view('dashboard', [
            'stats' => $stats,
            'role' => $role,
            'projektet_e_fundit' => $projektet_e_fundit,
            'projektet_e_mia' => $projektet_e_mia,
            'detyrat_e_ardhshme' => $detyrat_e_ardhshme
        ]);
    }
}
