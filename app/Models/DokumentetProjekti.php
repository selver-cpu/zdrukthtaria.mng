<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class DokumentetProjekti extends Model
{
    protected $table = 'dokumentet_projekti';
    protected $primaryKey = 'dokument_id';
    
    protected $fillable = [
        'projekt_id',
        'emri_skedarit',
        'lloji_skedarit',
        'rruga_skedarit',
        'madhesia_skedarit',
        'perdorues_id_ngarkues',
        'pershkrimi',
        'kategoria'
    ];

    protected $casts = [
        'data_ngarkimit' => 'datetime',
        'madhesia_skedarit' => 'integer'
    ];

    public function projekti(): BelongsTo
    {
        return $this->belongsTo(Projekti::class, 'projekt_id', 'projekt_id');
    }

    public function ngartuesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perdorues_id_ngarkues', 'perdorues_id');
    }
}
