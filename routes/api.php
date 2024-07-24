<?php

use App\Http\Controllers\TacheController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('taches', [TacheController::class, 'store']);
Route::get('taches', [TacheController::class, 'index']);
Route::get('taches/{tache:uid}', [TacheController::class, 'show']);
Route::put('taches/{tache:uid}/done', [TacheController::class, 'terminer']);
Route::put('taches/{tache:uid}', [TacheController::class, 'update']);
Route::delete('taches/{tache:uid}', [TacheController::class, 'destroy']);
