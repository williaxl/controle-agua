<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterTariffController;

Route::get('/', [WaterTariffController::class, 'index'])->name('water.index');
Route::post('/calcular', [WaterTariffController::class, 'calculate'])->name('water.calculate');
