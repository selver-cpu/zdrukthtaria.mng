<?php

namespace App\Traits;

use App\Models\DitarVeprimet;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getActivitiesToLog() as $event) {
            static::$event(function ($model) use ($event) {
                $model->logActivity($event);
            });
        }
    }

    protected function logActivity($description)
    {
        DitarVeprimet::create([
            'perdorues_id' => Auth::id(),
            'veprimi' => $this->getActivityDescription($description),
            // Use getKey() so custom primary keys (material_id, projekt_id, etc.) are logged correctly
            'objekt_id' => $this->getKey(),
            'objekt_tipi' => get_class($this),
            'ip_adresa' => request()->ip(),
        ]);
    }

    protected function getActivityDescription($event)
    {
        return "{$event} " . strtolower(class_basename($this));
    }

    protected static function getActivitiesToLog()
    {
        return ['created', 'updated', 'deleted'];
    }
}
