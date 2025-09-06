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
    ];

    public function projektet()
    {
        return $this->belongsToMany(Projektet::class, 'projekt_materiale', 'material_id', 'projekt_id')
                    ->withPivot('sasia_perdorur', 'projekt_material_id');
    }
}
