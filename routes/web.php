<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\DepartamentosController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\MailController;


Route::get('/', function () {
    return view('auth.login');
});
Auth::routes();

Route::get('/auth', [HomeController::class, 'index'])->name('home');

// Rutas para administrador
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:super-admin']], function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('negocios', [AdminController::class, 'negocios'])->name('admin.negocios');
    Route::get('negocios/json', [AdminController::class, 'negocios_json'])->name('admin.negocios.json');
    Route::post('negocios', [AdminController::class, 'store_negocio'])->name('admin.negocios.store');
    Route::get('negocios/new/', [AdminController::class, 'new_negocio'])->name('admin.negocios.new');
    Route::get('negocios/{id}/pagos', [AdminController::class, 'registrar_pago'])->name('admin.negocios.pagos');
    Route::get('settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
    Route::post('usuarios', [AdminController::class, 'registrar_usuario'])->name('admin.usuarios.store');

    Route::get('planes', [PlanController::class, 'index'])->name('admin.planes');
    Route::post('planes', [PlanController::class, 'store'])->name('admin.planes.store');
    Route::get('planes/{id}', [PlanController::class, 'read'])->name('admin.planes.read');
    Route::post('planes/{id}', [PlanController::class, 'update'])->name('admin.planes.update');
    Route::get('negocios/{id}/upgrade', [PlanController::class, 'mejorar_plan'])->name('admin.negocios.upgrade');
});

// Rutas para negocio
Route::group(['prefix' => 'business', 'middleware' => ['auth', 'role:negocio|vendedor']], function () {
    Route::get('dashboard', [BusinessController::class, 'dashboard'])->name('business.dashboard');
    Route::get('factura', [BusinessController::class, 'factura'])->name('business.factura');
    Route::get('clientes', [BusinessController::class, 'clientes'])->name('business.clientes');
    Route::get('sucursales' , [BusinessController::class, 'sucursales'])->name('business.sucursales');
    Route::get('productos', [BusinessController::class, 'productos'])->name('business.productos');
    Route::get('dtes', [BusinessController::class, 'dtes'])->name('business.dtes');
    Route::post("/enviar_dte", [MailController::class, "mandar_correo"])->name("invoices.send");
    Route::post('factura', [BusinessController::class, 'send_dte'])->name('business.factura.send');
});

Route::get('/catalogo/{codigo}', [CatalogoController::class, 'getValues'])->name('catalogo.getValues');
Route::get('/departamentos', [DepartamentosController::class, 'getDepartamentos'])->name('departamentos.getDepartamentos');
Route::get('/departamentos/all', [DepartamentosController::class, 'getAll'])->name('departamentos.getAll');
Route::get('/municipios/{departamento}', [DepartamentosController::class, 'getMunicipios'])->name('departamentos.getMunicipios');
