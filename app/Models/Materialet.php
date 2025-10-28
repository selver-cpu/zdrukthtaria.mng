<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Materialet extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'materialet';
    protected $primaryKey = 'material_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = 'data_perditesimit';

    protected $fillable = [
        'emri_materialit',
        'njesia_matese',
        'pershkrimi',
        'sasia_stokut',
        'sasia_minimale',
        'sasia_rezervuar',
        'cmimi_per_njesi',
        'lokacioni',
        'alert_low_stock',
    ];

    protected $casts = [
        'sasia_stokut' => 'decimal:2',
        'sasia_minimale' => 'decimal:2',
        'sasia_rezervuar' => 'decimal:2',
        'cmimi_per_njesi' => 'decimal:2',
        'alert_low_stock' => 'boolean',
    ];

    public function projektet()
    {
        return $this->belongsToMany(Projektet::class, 'projekt_materiale', 'material_id', 'projekt_id')
                    ->withPivot('sasia_perdorur', 'projekt_material_id');
    }

    public function dimensions()
    {
        return $this->hasMany(ProjektetDimensions::class, 'materiali_id', 'material_id');
    }

    /**
     * Kontrollon nëse ka stok të mjaftueshëm
     */
    public function kaStokTeMjaftueshem($sasia_nevojitur)
    {
        return ($this->sasia_stokut - $this->sasia_rezervuar) >= $sasia_nevojitur;
    }

    /**
     * Merr sasinë e disponueshme
     */
    public function sasiaEDisponueshme()
    {
        return $this->sasia_stokut - $this->sasia_rezervuar;
    }

    /**
     * Kontrollon nëse është stok i ulët
     */
    public function eshteStokIUlet()
    {
        return $this->sasiaEDisponueshme() <= $this->sasia_minimale;
    }

    /**
     * Rezervon sasi për një projekt
     */
    public function rezervoSasi($sasia)
    {
        $this->increment('sasia_rezervuar', $sasia);

        if ($this->eshteStokIUlet()) {
            $this->update(['alert_low_stock' => true]);
        }
    }

    /**
     * Liron sasinë e rezervuar
     */
    public function liroRezervimin($sasia)
    {
        $this->decrement('sasia_rezervuar', $sasia);

        if (!$this->eshteStokIUlet()) {
            $this->update(['alert_low_stock' => false]);
        }
    }

    /**
     * Përditëson stokun pas përdorimit
     */
    public function perditesoStokun($sasia_perdorur)
    {
        $this->decrement('sasia_stokut', $sasia_perdorur);
        $this->decrement('sasia_rezervuar', $sasia_perdorur);

        if ($this->eshteStokIUlet()) {
            $this->update(['alert_low_stock' => true]);
        }
    }
}
