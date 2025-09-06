<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesiProjektit extends Model
{
    use HasFactory;

    protected $table = 'procesi_projektit';
    protected $primaryKey = 'proces_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'projekt_id',
        'status_id',
        'perdorues_id',
        'komente',
        'data_ndryshimit',
    ];

    protected $casts = [
        'data_ndryshimit' => 'datetime',
    ];

    /**
     * Projekti të cilit i përket ky proces
     */
    public function projekt()
    {
        return $this->belongsTo(Projektet::class, 'projekt_id', 'projekt_id');
    }

    /**
     * Statusi i projektit në këtë proces
     */
    public function statusi_projektit()
    {
        return $this->belongsTo(StatusetProjektit::class, 'status_id', 'status_id');
    }

    /**
     * Përdoruesi që ka regjistruar këtë proces
     */
    public function perdoruesi()
    {
        return $this->belongsTo(User::class, 'perdorues_id', 'perdorues_id');
    }

    /**
     * Dokumentet e bashkangjitura me këtë proces
     */
    public function dokumente()
    {
        return $this->hasMany(DokumentetProjekti::class, 'proces_id', 'proces_id');
    }
}
