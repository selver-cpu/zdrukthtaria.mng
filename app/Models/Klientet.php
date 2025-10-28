<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
class Klientet extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'klientet';
    protected $primaryKey = 'klient_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = 'data_perditesimit';

    protected $fillable = [
        'emri_klientit',
        'person_kontakti',
        'telefon_kontakt',
        'email_kontakt',
        'adresa_faktura',
        'qyteti',
        'kodi_postal',
        'shteti',
        'shenime'
    ];

    public function getRouteKeyName()
    {
        return 'klient_id';
    }

    public function projektet()
    {
        return $this->hasMany(Projektet::class, 'klient_id', 'klient_id');
    }
}
