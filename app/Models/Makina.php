<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Makina extends Model
{
    use HasFactory;

    protected $table = 'makinat';
    protected $primaryKey = 'makin_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = 'data_perditesimit';

    public const LLOJET = [
        'cnc' => 'CNC Router',
        'panel_saw' => 'Sharrë Paneli',
        'edge_bander' => 'Kantues / Edge Bander',
        'dust_collector' => 'Aspirim Pluhuri',
        'sander' => 'Lëmues',
        'drill' => 'Shpues / Boring',
        'compressor' => 'Kompresor',
        'other' => 'Tjetër',
    ];

    public const STATUSET = [
        'aktive' => 'Aktive',
        'ne_mirembajtje' => 'Në mirëmbajtje',
        'jashte_funksioni' => 'Jashtë funksionit',
    ];

    protected $fillable = [
        'emri',
        'lloji',
        'marka',
        'modeli',
        'numri_serik',
        'lokacioni',
        'statusi',
        'kritikaliteti',
        'data_blerjes',
        'ore_pune',
        'shenime',
    ];

    protected $casts = [
        'data_blerjes' => 'date',
        'ore_pune' => 'decimal:1',
    ];

    public function planet(): HasMany
    {
        return $this->hasMany(MirembajtjePlan::class, 'makin_id', 'makin_id');
    }

    public function evidenca(): HasMany
    {
        return $this->hasMany(MirembajtjeEvidenca::class, 'makin_id', 'makin_id');
    }

    public function planetAktive(): HasMany
    {
        return $this->planet()->where('aktiv', true);
    }

    public function planetVonuar(): HasMany
    {
        return $this->planetAktive()
            ->whereNotNull('data_radhese')
            ->whereDate('data_radhese', '<', now()->toDateString());
    }

    public function getLlojiLabelAttribute(): string
    {
        return self::LLOJET[$this->lloji] ?? $this->lloji;
    }

    public function getStatusiLabelAttribute(): string
    {
        return self::STATUSET[$this->statusi] ?? $this->statusi;
    }
}
