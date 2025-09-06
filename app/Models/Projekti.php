<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DokumentetProjekti;
use App\Models\Klientet;
use App\Models\StatusetProjektit;
use App\Models\User;
use App\Models\ProcesiProjektit;

class Projekti extends Model
{
    protected $table = 'projektet';
    protected $primaryKey = 'projekt_id';
    
    protected $fillable = [
        'klient_id',
        'emri_projektit',
        'pershkrimi',
        'data_fillimit_parashikuar',
        'data_perfundimit_parashikuar',
        'data_perfundimit_real',
        'status_id',
        'mjeshtri_caktuar_id',
        'montuesi_caktuar_id',
        'shenime_projekt'
    ];

    protected $casts = [
        'data_fillimit_parashikuar' => 'date',
        'data_perfundimit_parashikuar' => 'date',
        'data_perfundimit_real' => 'date',
    ];

    public function klienti(): BelongsTo
    {
        return $this->belongsTo(Klientet::class, 'klient_id', 'klient_id');
    }

    public function statusi_projektit(): BelongsTo
    {
        return $this->belongsTo(StatusetProjektit::class, 'status_id', 'status_id');
    }

    public function mjeshtri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mjeshtri_caktuar_id', 'perdorues_id');
    }

    public function montuesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'montuesi_caktuar_id', 'perdorues_id');
    }

    public function dokumentet(): HasMany
    {
        return $this->hasMany(DokumentetProjekti::class, 'projekt_id', 'projekt_id');
    }

    public function proceset(): HasMany
    {
        return $this->hasMany(ProcesiProjektit::class, 'projekt_id', 'projekt_id');
    }
}
