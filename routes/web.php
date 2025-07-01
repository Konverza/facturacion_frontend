<?php

use App\Http\Controllers\ConsultaController;

require base_path('routes/admin.php');
require base_path('routes/business.php');
require base_path('routes/auth.php');

Route::get('/consulta', [ConsultaController::class, 'index'])->name('consulta');
Route::post('/consulta', [ConsultaController::class, 'search'])->name('consulta.search');
Route::get('/consulta/{codGeneracion}', [ConsultaController::class, 'show'])->name('consulta.show');
Route::get('/ambiente', [\App\Http\Controllers\AmbienteController::class, 'index'])->name('ambiente.index');