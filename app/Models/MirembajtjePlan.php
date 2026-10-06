<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MirembajtjePlan extends Model
{
    use HasFactory;

    protected $table = 'mirembajtje_planet';
    protected $primaryKey = 'plan_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = 'data_perditesimit';

    public const FREKUENCAT = [
        'dite' => 'Ditore',
        'jave' => 'Javore',
        'muaj' => 'Mujore',
        'tremujor' => 'Tremujore',
        'vit' => 'Vjetore',
        'ore' => 'Sipas orëve të punës',
    ];

    protected $fillable = [
        'makin_id',
        'titulli',
        'checklist',
        'frekuenca',
        'intervali',
        'data_fundit_kryer',
        'data_radhese',
        'ore_fundit_kryer',
        'aktiv',
    ];

    protected $casts = [
        'data_fundit_kryer' => 'date',
        'data_radhese' => 'date',
        'ore_fundit_kryer' => 'decimal:1',
        'aktiv' => 'boolean',
        'intervali' => 'integer',
    ];

    public function makina(): BelongsTo
    {
        return $this->belongsTo(Makina::class, 'makin_id', 'makin_id');
    }

    public function evidenca(): HasMany
    {
        return $this->hasMany(MirembajtjeEvidenca::class, 'plan_id', 'plan_id');
    }

    public function eshteVonuar(): bool
    {
        if (!$this->aktiv || !$this->data_radhese) {
            return false;
        }

        if ($this->frekuenca === 'ore') {
            $makina = $this->makina;
            if (!$makina || $this->ore_fundit_kryer === null) {
                return false;
            }

            return ($makina->ore_pune - $this->ore_fundit_kryer) >= $this->intervali;
        }

        return $this->data_radhese->lt(now()->startOfDay());
    }

    public function eshteSot(): bool
    {
        return $this->aktiv
            && $this->data_radhese
            && $this->frekuenca !== 'ore'
            && $this->data_radhese->isToday();
    }

    public function getFrekuencaLabelAttribute(): string
    {
        $base = self::FREKUENCAT[$this->frekuenca] ?? $this->frekuenca;
        if ($this->intervali > 1) {
            return "Çdo {$this->intervali} " . strtolower($base);
        }

        return $base;
    }

    public function getChecklistItemsAttribute(): array
    {
        if (!$this->checklist) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->checklist))));
    }

    /**
     * Mark plan as completed and compute next due date / hour threshold.
     */
    public function shenoSiTeKryer(?Carbon $dataKryerjes = null, ?float $oreAktuale = null): void
    {
        $data = $dataKryerjes ? $dataKryerjes->copy() : now();
        $this->data_fundit_kryer = $data->toDateString();

        if ($this->frekuenca === 'ore') {
            $ore = $oreAktuale ?? (float) ($this->makina->ore_pune ?? 0);
            $this->ore_fundit_kryer = $ore;
            // Approximate next calendar reminder for visibility (1 day placeholder)
            $this->data_radhese = $data->copy()->addDay()->toDateString();
        } else {
            $this->data_radhese = $this->llogaritDatenRadhese($data)->toDateString();
        }

        $this->save();
    }

    public function llogaritDatenRadhese(Carbon $nga): Carbon
    {
        $interval = max(1, (int) $this->intervali);

        return match ($this->frekuenca) {
            'dite' => $nga->copy()->addDays($interval),
            'jave' => $nga->copy()->addWeeks($interval),
            'muaj' => $nga->copy()->addMonths($interval),
            'tremujor' => $nga->copy()->addMonths(3 * $interval),
            'vit' => $nga->copy()->addYears($interval),
            default => $nga->copy()->addDays($interval),
        };
    }
}
