<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusetProjektit extends Model
{
    use HasFactory;

    protected $table = 'statuset_projektit';
    protected $primaryKey = 'status_id';
    public $timestamps = false;

    protected $fillable = [
        'emri_statusit',
        'pershkrimi',
        'renditja',
        'klasa_css',
    ];

    public function projektet()
    {
        return $this->hasMany(Projektet::class, 'status_id', 'status_id');
    }
}
