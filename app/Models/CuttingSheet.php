<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuttingSheet extends Model
{
    protected $table = 'cutting_sheets';
    protected $primaryKey = 'sheet_id';
    
    protected $fillable = [
        'plan_id',
        'gjeresia_fletit',
        'gjatesia_fletit',
        'trashesia_fletit',
        'pozicionet_prerjeve',
        'perqindja_perdorimit',
        'siperfaqja_perdorur',
        'siperfaqja_totale',
        'shenime'
    ];
    
    protected $casts = [
        'gjeresia_fletit' => 'decimal:2',
        'gjatesia_fletit' => 'decimal:2',
        'trashesia_fletit' => 'decimal:2',
        'pozicionet_prerjeve' => 'array',
        'perqindja_perdorimit' => 'decimal:2',
        'siperfaqja_perdorur' => 'decimal:2',
        'siperfaqja_totale' => 'decimal:2'
    ];
    
    /**
     * Marrëdhënia me planin e prerjes
     */
    public function cuttingPlan(): BelongsTo
    {
        return $this->belongsTo(CuttingPlan::class, 'plan_id', 'plan_id');
    }
}
