<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'rolet';
    protected $primaryKey = 'rol_id';
    
    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = null; // Nuk ka kolonë për updated_at

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emri_rolit',
        'pershkrimi',
    ];

    /**
     * Merr përdoruesit që kanë këtë rol.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'rol_id', 'rol_id');
    }
}
