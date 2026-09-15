<?php

use App\Http\Controllers\OffreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/offres', [OffreController::class, 'index'])
    ->name('offres.index');
    
    Route::get('/offres/create', [OffreController::class, 'create'])
    ->name('offres.create');

Route::post('/offres', [OffreController::class, 'store'])
    ->name('offres.store');
    Route::get('/offres/{offre}/edit', [OffreController::class, 'edit'])
    ->name('offres.edit');

Route::put('/offres/{offre}', [OffreController::class, 'update'])
    ->name('offres.update');
    Route::delete('/offres/{offre}', [OffreController::class, 'destroy'])
    ->name('offres.destroy');