<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DitarVeprimet extends Model
{
    protected $table = 'ditar_veprimet';
    protected $primaryKey = 'ditar_id';

    const CREATED_AT = 'data_veprimit';
    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'perdorues_id',
        'veprimi',
        'objekt_id',
        'objekt_tipi',
        'ip_adresa',
        'te_dhenat_e_reja'
    ];

    protected $casts = [
        'data_veprimit' => 'datetime'
    ];

    /**
     * Merr përdoruesin që ka kryer veprimin.
     */
    public function perdoruesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perdorues_id');
    }
}
