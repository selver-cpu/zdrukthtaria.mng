<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjektMateriale extends Model
{
    use HasFactory;

    protected $table = 'projekt_materiale';
    protected $primaryKey = 'projekt_material_id';

    public $timestamps = false; // Tabela nuk ka kolona created_at/updated_at

    protected $fillable = [
        'projekt_id',
        'material_id',
        'sasia_perdorur',
    ];

    public function projekt()
    {
        return $this->belongsTo(Projektet::class, 'projekt_id', 'projekt_id');
    }

    public function material()
    {
        return $this->belongsTo(Materialet::class, 'material_id', 'material_id');
    }
}
