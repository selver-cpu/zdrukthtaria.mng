<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\CuttingPlan;
use App\Models\CuttingSheet;

echo "Duke filluar pastrimin e planeve të prerjes të përsëritura...\n";

DB::beginTransaction();

try {
    // Marrim të gjitha kombinimet unike të design_id dhe material_id
    $designs = DB::table('cutting_plans')
        ->select('design_id', 'material_id')
        ->distinct()
        ->get();
    
    $totalDeleted = 0;
    
    foreach ($designs as $design) {
        // Marrim të gjitha planet e prerjes për këtë kombinim
        $plans = DB::table('cutting_plans')
            ->where('design_id', $design->design_id)
            ->where('material_id', $design->material_id)
            ->orderBy('plan_id', 'asc')
            ->get();
        
        // Nëse ka më shumë se një plan prerje për këtë kombinim
        if (count($plans) > 1) {
            $keepPlanId = $plans[0]->plan_id;
            echo "Duke mbajtur planin e prerjes ID: {$keepPlanId} për dizajnin {$design->design_id} dhe materialin {$design->material_id}\n";
            
            foreach ($plans as $index => $plan) {
                if ($index > 0) {
                    // Fshijmë fletët e prerjes për këtë plan
                    $deletedSheets = DB::table('cutting_sheets')
                        ->where('plan_id', $plan->plan_id)
                        ->delete();
                    
                    // Fshijmë planin e prerjes
                    DB::table('cutting_plans')
                        ->where('plan_id', $plan->plan_id)
                        ->delete();
                    
                    echo "Fshiva planin e prerjes ID: {$plan->plan_id}\n";
                    $totalDeleted++;
                }
            }
        }
    }
    
    DB::commit();
    echo "Pastrimi përfundoi me sukses! U fshinë {$totalDeleted} plane prerje të përsëritura.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Gabim: " . $e->getMessage() . "\n";
}
