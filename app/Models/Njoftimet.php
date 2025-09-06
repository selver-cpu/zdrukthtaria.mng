<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Njoftimet extends Model
{
    use HasFactory;

    protected $table = 'njoftimet';
    protected $primaryKey = 'njoftim_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'perdorues_id',
        'projekt_id',
        'mesazhi',
        'lloji_njoftimit',
        'lexuar'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'lexuar' => 'boolean',
        'data_krijimit' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($njoftim) {
            if (!in_array($njoftim->lloji_njoftimit, ['email', 'sms', 'system'])) {
                throw new \InvalidArgumentException('Lloji i njoftimit duhet të jetë një nga: email, sms, system');
            }
        });
    }

    public function perdorues()
    {
        return $this->belongsTo(User::class, 'perdorues_id', 'perdorues_id');
    }

    public function projekt()
    {
        return $this->belongsTo(Projektet::class, 'projekt_id', 'projekt_id');
    }
}
