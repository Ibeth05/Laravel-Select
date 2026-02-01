<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::get('/', fn() => redirect()->route('productos.index'));

Route::resource('productos', ProductoController::class);

// Crear movimiento (Ingreso/Egreso/Ajuste)
Route::post('productos/{producto}/movimiento', [ProductoController::class, 'moverInventario'])
    ->name('productos.movimiento');

// Historial (opcional)
Route::get('productos/{producto}/movimientos', [ProductoController::class, 'movimientos'])
    ->name('productos.movimientos');