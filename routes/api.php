<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\GeneroController;
use App\Models\User;

Route::apiResource('libros', LibroController::class);
Route::apiResource('prestamos', PrestamoController::class);
Route::get('/estadisticas', [EstadisticaController::class, 'index']);
Route::get('/autores', [AutorController::class, 'index']);
Route::get('/generos', [GeneroController::class, 'index']);

Route::get('/usuarios', function () {
    return User::select('id', 'name')->get();
});