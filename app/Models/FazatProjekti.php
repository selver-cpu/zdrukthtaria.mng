<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FazatProjekti extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'fazat_projekti';

    protected $fillable = [
        'emri_fazes',
        'pershkrimi',
        'renditja',
    ];

    /**
     * Marrëdhënia për projektet që përfshijnë këtë fazë pune.
     */
    public function projektet()
    {
        return $this->belongsToMany(Projektet::class, 'projekt_faza_pune', 'faza_id', 'projekt_id')
                    ->withPivot(['statusi_fazes', 'data_fillimit', 'data_perfundimit'])
                    ->using(ProjektFazaPune::class);
    }
}
