<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rolet extends Model
{
    use HasFactory;

    protected $table = 'rolet';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = [
        'emri_rolit',
        'pershkrimi',
    ];

    public function perdoruesit()
    {
        return $this->hasMany(User::class, 'rol_id', 'rol_id');
    }
}
