<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\ValidatesProjektFazat;

class ProjektFazaPune extends Pivot
{
    use ValidatesProjektFazat;
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'projekt_faza_pune';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'projekt_id',
        'faza_id',
        'data_fillimit',
        'data_perfundimit',
        'komente',
        'statusi_fazes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_fillimit' => 'datetime',
        'data_perfundimit' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Përfundo fazën aktuale dhe përditëso statusin e projektit.
     *
     * @param string $komente
     * @return bool
     */
    public function perfundoFazen(string $komente = ''): bool
    {
        $this->data_perfundimit = now();
        $this->komente = $komente;
        
        if ($this->save()) {
            // Kontrollo nëse është faza e fundit
            $fazaRadhes = $this->getFazaRadhes($this->projekt_id);
            
            if (!$fazaRadhes) {
                // Nëse nuk ka fazë tjetër, projekti është gati për montim
                $this->projekt->update(['status_id' => 4]); // 4 = Gati për Montim
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Shto një fazë të re për projektin.
     *
     * @param int $projektId
     * @param int $fazaId
     * @param string $komente
     * @return ProjektFazaPune|null
     */
    public static function shtoFaze(int $projektId, int $fazaId, string $komente = ''): ?ProjektFazaPune
    {
        $instance = new static;
        
        if (!$instance->validateFazaOrder($projektId, $fazaId)) {
            return null;
        }

        return static::create([
            'projekt_id' => $projektId,
            'faza_id' => $fazaId,
            'data_fillimit' => now(),
            'komente' => $komente
        ]);
    }

    /**
     * Marrëdhënia me projektin
     */
    public function projekt()
    {
        return $this->belongsTo(Projektet::class, 'projekt_id', 'projekt_id');
    }

    /**
     * Marrëdhënia me fazën
     */
    public function faza()
    {
        return $this->belongsTo(FazatProjekti::class, 'faza_id', 'id');
    }
}
