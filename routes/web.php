<?php

use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\Business\CustomerContoller;
use App\Http\Controllers\Admin\BusinessController;

require base_path('routes/admin.php');
require base_path('routes/business.php');
require base_path('routes/auth.php');

Route::get('/consulta', [ConsultaController::class, 'index'])->name('consulta');
Route::post('/consulta', [ConsultaController::class, 'search'])->name('consulta.search');
Route::get('/consulta/{codGeneracion}', [ConsultaController::class, 'show'])->name('consulta.show');
Route::get('/ambiente', [\App\Http\Controllers\AmbienteController::class, 'index'])->name('ambiente.index');

// Rutas públicas para registro de clientes
Route::get('/registro-clientes/{nit}', [CustomerContoller::class, 'showPublicRegistration'])->name('registro-clientes');
Route::post('/registro-clientes/{nit}', [CustomerContoller::class, 'storePublicRegistration'])->name('registro-clientes.store');
Route::get('/api/municipios/', [BusinessController::class, "getMunicipios"])->name('api.municipios');