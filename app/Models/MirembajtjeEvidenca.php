<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MirembajtjeEvidenca extends Model
{
    use HasFactory;

    protected $table = 'mirembajtje_evidenca';
    protected $primaryKey = 'evidenca_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = 'data_perditesimit';

    public const LLOJET = [
        'preventive' => 'Parandaluese (PM)',
        'corrective' => 'Korrektuese (riparim)',
        'inspection' => 'Inspektim',
    ];

    public const STATUSET = [
        'e_hapur' => 'E hapur',
        'ne_progres' => 'Në progres',
        'perfunduar' => 'Përfunduar',
    ];

    public const PRIORITETET = [
        'ulet' => 'Ulët',
        'mesatar' => 'Mesatar',
        'larte' => 'Lartë',
        'urgjent' => 'Urgjent',
    ];

    protected $fillable = [
        'makin_id',
        'plan_id',
        'lloji',
        'titulli',
        'pershkrimi',
        'statusi',
        'prioriteti',
        'perdorues_id',
        'data_planifikuar',
        'data_kryerjes',
        'kohezgjatja_minuta',
        'kosto',
        'pjeset_e_perdorura',
        'gjetjet',
    ];

    protected $casts = [
        'data_planifikuar' => 'date',
        'data_kryerjes' => 'datetime',
        'kosto' => 'decimal:2',
        'kohezgjatja_minuta' => 'integer',
    ];

    public function makina(): BelongsTo
    {
        return $this->belongsTo(Makina::class, 'makin_id', 'makin_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MirembajtjePlan::class, 'plan_id', 'plan_id');
    }

    public function perdoruesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perdorues_id', 'perdorues_id');
    }

    public function getLlojiLabelAttribute(): string
    {
        return self::LLOJET[$this->lloji] ?? $this->lloji;
    }

    public function getStatusiLabelAttribute(): string
    {
        return self::STATUSET[$this->statusi] ?? $this->statusi;
    }

    public function getPrioritetiLabelAttribute(): string
    {
        return self::PRIORITETET[$this->prioriteti] ?? $this->prioriteti;
    }
}
