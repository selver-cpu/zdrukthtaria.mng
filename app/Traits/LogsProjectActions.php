<?php

namespace App\Traits;

use App\Models\DitarVeprimet;
use Illuminate\Support\Facades\Auth;

trait LogsProjectActions
{
    protected function logProjectAction($projekt_id, $veprimi, $te_dhenat = null)
    {
        DitarVeprimet::create([
            'perdorues_id' => Auth::check() ? Auth::user()->perdorues_id : null,
            'veprimi' => $veprimi,
            'objekt_id' => $projekt_id,
            'objekt_tipi' => 'projekt',
            'ip_adresa' => request()->ip(),
            'data_veprimit' => now(),
            'te_dhenat_e_reja' => $te_dhenat ? json_encode($te_dhenat) : null
        ]);
    }
}
