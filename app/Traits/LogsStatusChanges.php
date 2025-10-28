<?php

namespace App\Traits;

use App\Models\DitarVeprimet;
use Illuminate\Support\Facades\Auth;

trait LogsStatusChanges
{
    /**
     * Regjistro ndryshimin e statusit në ditar.
     *
     * @param string $veprimi
     * @param mixed $teDhenatEVjetra
     * @param mixed $teDhenatEReja
     * @return void
     */
    protected function logStatusChange(string $veprimi, $teDhenatEVjetra = null, $teDhenatEReja = null): void
    {
        $detajetVeprimit = $veprimi;
        if ($teDhenatEVjetra || $teDhenatEReja) {
            $detajetVeprimit .= ' - Nga: ' . json_encode($teDhenatEVjetra);
            $detajetVeprimit .= ' Në: ' . json_encode($teDhenatEReja);
        }

        DitarVeprimet::create([
            'perdorues_id' => Auth::id(),
            'veprimi' => $detajetVeprimit,
            'objekt_id' => $this->getKey(),
            'objekt_tipi' => $this->getTable(),
            'ip_adresa' => request()->ip(),
            'data_veprimit' => now()
        ]);
    }

    /**
     * Boot metodat e trait-it.
     */
    public static function bootLogsStatusChanges()
    {
        static::updating(function ($model) {
            // Kontrollo nëse ka ndryshime në statusin e projektit
            if ($model->isDirty('status_id')) {
                $oldStatus = $model->getOriginal('status_id');
                $newStatus = $model->status_id;
                
                $model->logStatusChange(
                    'Ndryshim statusi projekti',
                    ['status_id' => $oldStatus],
                    ['status_id' => $newStatus]
                );
            }
        });

        static::created(function ($model) {
            $model->logStatusChange(
                'Krijim i ri',
                null,
                $model->getAttributes()
            );
        });

        static::deleted(function ($model) {
            $model->logStatusChange(
                'Fshirje',
                $model->getOriginal(),
                null
            );
        });
    }
}
