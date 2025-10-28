<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjektetController;
use App\Http\Controllers\KlientetController;
use App\Http\Controllers\StatusetProjektitController;
use App\Http\Controllers\MaterialetController;
use App\Http\Controllers\DokumentetProjektiController;
use App\Http\Controllers\StafiController;
use App\Http\Controllers\FazatProjektiController;
use App\Http\Controllers\DitarVeprimetController;
use App\Http\Controllers\NjoftimetController;
use App\Http\Controllers\RaportetController;
use App\Http\Controllers\ProjektMaterialeController;
use App\Http\Controllers\ProcesiProjektitController;
use App\Http\Controllers\ProjektetDimensionsController;
use App\Http\Controllers\TicketLayoutController;
use App\Http\Controllers\CutlistOptimizerController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rruga për testimin e databazës
    Route::get('/test-db-insert', [ProjektetController::class, 'testInsert'])->name('test.db.insert');
});

require __DIR__.'/auth.php';

Route::resource('projektet', ProjektetController::class)->parameters(['projektet' => 'projekt'])->middleware(['auth']);

Route::resource('klientet', KlientetController::class)->middleware(['auth']);

Route::resource('statuset', StatusetProjektitController::class)->middleware(['auth']);

// Rrugët specifike për projektet
Route::prefix('projektet/{projekt}')->middleware(['auth'])->group(function () {
    // Materialet e projektit
    Route::post('/material', [ProjektetController::class, 'storeMaterial'])
        ->name('projektet.material.store')
        ->where('projekt', '[0-9]+');

    Route::put('/material/{material}', [ProjektetController::class, 'updateMaterial'])
        ->name('projektet.material.update')
        ->where(['projekt' => '[0-9]+', 'material' => '[0-9]+']);

    Route::get('/email', [ProjektetController::class, 'getUsersForEmail'])
        ->name('projektet.email-form')
        ->where('projekt', '[0-9]+');

    Route::post('/send-email', [ProjektetController::class, 'sendEmail'])
        ->name('projektet.send-email')
        ->where('projekt', '[0-9]+');

    Route::delete('/material/{material}', [ProjektetController::class, 'destroyMaterial'])
        ->name('projektet.material.destroy')
        ->where(['projekt' => '[0-9]+', 'material' => '[0-9]+']);

    // Project Phases
    Route::post('/faza', [ProjektetController::class, 'storeFaza'])->name('projektet.faza.store');
    Route::put('/faza/{faza_pivot_id}', [ProjektetController::class, 'updateFaza'])->name('projektet.faza.update');
    Route::delete('/faza/{faza_pivot_id}', [ProjektetController::class, 'destroyFaza'])->name('projektet.faza.destroy');

    // Project Documents
    Route::post('/dokumentet', [DokumentetProjektiController::class, 'store'])->name('projektet.dokumentet.store');
    Route::get('/dokumentet/{id}', [DokumentetProjektiController::class, 'show'])->name('projektet.dokumentet.show');
    Route::get('/dokumentet/{id}/view', [DokumentetProjektiController::class, 'view'])->name('projektet.dokumentet.view');
    Route::get('/dokumentet/{id}/download', [DokumentetProjektiController::class, 'download'])->name('projektet.dokumentet.download');
    Route::put('/dokumentet/{id}', [DokumentetProjektiController::class, 'update'])->name('projektet.dokumentet.update');
    Route::delete('/dokumentet/{id}', [DokumentetProjektiController::class, 'destroy'])->name('projektet.dokumentet.destroy');
});

// Materialet routes
Route::resource('materialet', MaterialetController::class)->except(['destroy'])->middleware(['auth']);
Route::delete('materialet/{materialet}', [MaterialetController::class, 'destroy'])->name('materialet.destroy')->middleware('auth');
Route::resource('stafi', StafiController::class)->middleware(['auth']);
Route::resource('fazat-projekti', FazatProjektiController::class)->middleware(['auth']);

// Routes for Project Process
Route::get('projektet/{projekt}/procesi', [ProcesiProjektitController::class, 'index'])->name('procesi.index')->middleware(['auth']);
Route::post('projektet/{projekt}/procesi', [ProcesiProjektitController::class, 'store'])->name('procesi.store')->middleware(['auth']);
Route::get('projektet/{projekt}/procesi/{proces}', [ProcesiProjektitController::class, 'show'])->name('procesi.show')->middleware(['auth']);

// Ditar Veprimesh
Route::get('/ditar', [DitarVeprimetController::class, 'index'])->name('ditar.index')->middleware(['auth']);

// Routes per Materialet e Projektit
Route::post('/projekte/{projekt}/materiale', [ProjektMaterialeController::class, 'store'])->name('projekte.materiale.store')->middleware(['auth']);
Route::delete('/projekt-materiale/{projektMaterial}', [ProjektMaterialeController::class, 'destroy'])->name('projekt-materiale.destroy')->middleware(['auth']);

// Njoftimet
Route::prefix('njoftimet')->name('njoftimet.')->middleware('auth')->group(function () {
    Route::get('/', [NjoftimetController::class, 'index'])->name('index');
    Route::get('/{njoftim_id}/lexuar', [NjoftimetController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/lexuar-te-gjitha', [NjoftimetController::class, 'markAllAsRead'])->name('markAllAsRead');
});

// Routes for Reports and Statistics
Route::prefix('raportet')->name('raportet.')->middleware(['auth'])->group(function () {
    Route::get('/', [RaportetController::class, 'index'])->name('index');
    Route::get('/projektet', [RaportetController::class, 'projektet'])->name('projektet');
    Route::get('/materialet', [RaportetController::class, 'materialet'])->name('materialet');
    Route::get('/stafi', [RaportetController::class, 'stafi'])->name('stafi');
    Route::get('/financiar', [RaportetController::class, 'financiar'])->name('financiar');
    Route::post('/eksporto', [RaportetController::class, 'eksporto'])->name('eksporto');
});

// Routes for Export functionality
Route::prefix('eksporto')->name('eksporto.')->middleware(['auth'])->group(function () {
    Route::post('/pdf', [App\Http\Controllers\ExportController::class, 'exportPDF'])->name('pdf');
    Route::post('/excel', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('excel');
    Route::post('/image', [App\Http\Controllers\ExportController::class, 'exportImage'])->name('image');
    Route::post('/3d', [App\Http\Controllers\ExportController::class, 'export3D'])->name('3d');
});

// Routes for Dimensions Module - Dimensionet e Projekteve
Route::prefix('dimensionet')->name('projektet-dimensions.')->middleware(['auth'])->group(function () {
    Route::get('/', [ProjektetDimensionsController::class, 'index'])->name('index');
    Route::get('/krijo', [ProjektetDimensionsController::class, 'create'])->name('create');
    Route::post('/', [ProjektetDimensionsController::class, 'store'])->name('store');
    
    // Export XML për makinën OSI 2007
    Route::get('/export-xml', [ProjektetDimensionsController::class, 'exportXML'])->name('export-xml');
    
    // Material report - duhet të jetë para /{dimension}
    Route::get('/material-report', [ProjektetDimensionsController::class, 'materialReport'])->name('material-report');
    Route::get('/check-stock', [ProjektetDimensionsController::class, 'checkStock'])->name('check-stock');
    
    Route::get('/{dimension}', [ProjektetDimensionsController::class, 'show'])->name('show');
    Route::get('/{dimension}/edit', [ProjektetDimensionsController::class, 'edit'])->name('edit');
    Route::put('/{dimension}', [ProjektetDimensionsController::class, 'update'])->name('update');
    Route::delete('/{dimension}', [ProjektetDimensionsController::class, 'destroy'])->name('destroy');
    Route::get('/{dimension}/print-ticket', [ProjektetDimensionsController::class, 'printTicket'])->name('print-ticket');
});

// Short alias for dimensions
Route::get('/dim', function() {
    return redirect()->route('projektet-dimensions.index');
})->middleware(['auth'])->name('dimensions.shortcut');

// Routes for Ticket Layout Editor
Route::prefix('ticket-layout')->name('ticket-layout.')->middleware(['auth'])->group(function () {
    Route::get('/', [TicketLayoutController::class, 'index'])->name('index');
    Route::post('/update', [TicketLayoutController::class, 'update'])->name('update');
    Route::post('/reset', [TicketLayoutController::class, 'reset'])->name('reset');
    Route::get('/config', [TicketLayoutController::class, 'getConfig'])->name('config');
    Route::get('/preview', [TicketLayoutController::class, 'preview'])->name('preview');
    Route::get('/export-lprint', [TicketLayoutController::class, 'exportLPrint'])->name('export-lprint');
});

// Routes for Cutlist Optimizer
Route::prefix('cutlist-optimizer')->name('cutlist-optimizer.')->middleware(['auth'])->group(function () {
    Route::get('/', [CutlistOptimizerController::class, 'index'])->name('index');
    Route::post('/optimize', [CutlistOptimizerController::class, 'optimize'])->name('optimize');
    Route::get('/result/{id}', [CutlistOptimizerController::class, 'showResult'])->name('result');
});
