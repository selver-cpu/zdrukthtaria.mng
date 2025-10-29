<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;
use App\Traits\LogsStatusChanges;
use Illuminate\Database\Eloquent\Model;

class Projektet extends Model
{
    use HasFactory;
    use LogsActivity;
    use LogsStatusChanges;

    protected $table = 'projektet';
   protected $primaryKey = 'projekt_id';

    // Përdorim kolonat standarde të Laravel për timestamps: created_at dhe updated_at

    protected $fillable = [
        'klient_id',
        'emri_projektit',
        'buxheti',
        'pershkrimi',
        'data_fillimit_parashikuar',
        'data_perfundimit_parashikuar',
        'data_perfundimit_real',
        'status_id',
        'mjeshtri_caktuar_id',
        'montuesi_caktuar_id',
        'shenime_projekt',
        'krijues_id',
    ];

    protected $casts = [
        'data_fillimit_parashikuar' => 'datetime',
        'data_perfundimit_parashikuar' => 'datetime',
        'data_perfundimit_real' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function klient()
    {
        return $this->belongsTo(Klientet::class, 'klient_id', 'klient_id');
    }

    public function statusi_projektit()
    {
        return $this->belongsTo(StatusetProjektit::class, 'status_id', 'status_id');
    }

    public function mjeshtri()
    {
        return $this->belongsTo(User::class, 'mjeshtri_caktuar_id', 'perdorues_id');
    }

    public function fazat()
    {
        return $this->belongsToMany(FazatProjekti::class, 'projekt_faza_pune', 'projekt_id', 'faza_id')
            ->withPivot(['id', 'statusi_fazes', 'data_fillimit', 'data_perfundimit'])
            ->using(ProjektFazaPune::class);
    }

    public function montuesi()
    {
        return $this->belongsTo(User::class, 'montuesi_caktuar_id', 'perdorues_id');
    }

    public function materialet()
    {
        return $this->belongsToMany(Materialet::class, 'projekt_materiale', 'projekt_id', 'material_id')
                    ->withPivot('sasia_perdorur');
    }

    public function proceset()
    {
        return $this->hasMany(ProcesiProjektit::class, 'projekt_id', 'projekt_id');
    }

    public function dokumentet()
    {
        return $this->hasMany(DokumentetProjekti::class, 'projekt_id', 'projekt_id');
    }

    public function dimensions()
    {
        return $this->hasMany(ProjektetDimensions::class, 'projekt_id', 'projekt_id');
    }

    public function njoftime()
    {
        return $this->hasMany(Njoftimet::class, 'projekt_id', 'projekt_id');
    }
}
