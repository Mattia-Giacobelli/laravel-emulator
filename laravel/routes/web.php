<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RomController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Restituisce la home page con il form di caricamento e lista ROM
    return view('index', [
        'games' => \App\Models\Game::all()
    ]);
})->name('home');

// API Routes per gestione ROM Nintendo
Route::prefix('api')->group(function () {
    // Upload/Caricamento ROM
    Route::post('/rom/upload', [RomController::class, 'store'])->name('rom.upload');
    
    // Lista ROM disponibili
    Route::get('/roms', [RomController::class, 'index'])->name('roms.index');
    
    // Dettaglio singola ROM con informazioni streaming
    Route::get('/rom/{gameId}/info', [RomController::class, 'show'])->name('roms.show');
    
    // Elimina ROM
    Route::delete('/rom/{gameId}', [RomController::class, 'destroy'])->name('roms.destroy');
});
Route::prefix('api')->group(function () {
    // Upload/Caricamento ROM
    Route::post('/rom/upload', [RomController::class, 'store'])->name('rom.upload');

    // Lista ROM disponibili
    Route::get('/roms', [RomController::class, 'index'])->name('roms.index');

    // Dettaglio singola ROM con informazioni streaming
    Route::get('/rom/{gameId}/info', [RomController::class, 'show'])->name('roms.show');

    // Elimina ROM
    Route::delete('/rom/{gameId}', [RomController::class, 'destroy'])->name('roms.destroy');
});
