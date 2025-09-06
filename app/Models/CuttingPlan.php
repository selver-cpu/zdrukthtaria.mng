<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuttingPlan extends Model
{
    protected $table = 'cutting_plans';
    protected $primaryKey = 'plan_id';
    
    protected $fillable = [
        'design_id',
        'material_id',
        'plani_prerjes',
        'perqindja_mbeturinave',
        'numri_prerjeve',
        'trashesia_sharres',
        'shenime'
    ];
    
    protected $casts = [
        'plani_prerjes' => 'array',
        'perqindja_mbeturinave' => 'decimal:2',
        'trashesia_sharres' => 'decimal:2'
    ];
    
    /**
     * Marrëdhënia me dizajnin e raftit
     */
    public function shelfDesign(): BelongsTo
    {
        return $this->belongsTo(ShelfDesign::class, 'design_id', 'design_id');
    }
    
    /**
     * Marrëdhënia me materialin
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Materialet::class, 'material_id', 'material_id');
    }
    
    /**
     * Marrëdhënia me fletat e prerjes
     */
    public function cuttingSheets(): HasMany
    {
        return $this->hasMany(CuttingSheet::class, 'plan_id', 'plan_id');
    }
}
