<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlansController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SucursalController;


Route::middleware(['auth', 'role:admin'])->prefix("admin")->name("admin.")->group(function () {
    Route::get("/", [DashboardController::class, "index"])->name('index');
    Route::get("/dashboard", [DashboardController::class, "index"])->name('dashboard');
    Route::resource("/business", BusinessController::class);
    Route::resource("/plans", PlansController::class);
    Route::resource("/users", UserController::class);
    Route::get("/get-municipios", [BusinessController::class, "getMunicipios"])->name("get-municipios");

    // Sucursales
    Route::get("/business/{business_id}/sucursales", [SucursalController::class, "index"])->name(name: "sucursales.index");
    Route::post("/business/{business_id}/sucursales", [SucursalController::class, "store_sucursal"])->name("sucursales.store_sucursal");
    Route::get("/business/{business_id}/sucursales/{id}", [SucursalController::class, "edit"])->name("sucursales.edit");
    Route::put("/business/{business_id}/sucursales/{id}", [SucursalController::class, "update_sucursal"])->name("sucursales.update_sucursal");
    Route::delete("/business/{business_id}/sucursales/{id}", [SucursalController::class, "delete_sucursal"])->name("sucursales.delete_sucursal");
    // Punto de Venta
    Route::get("/business/{business_id}/sucursales/{sucursal_id}/puntos-venta", [SucursalController::class, "index_puntos_venta"])->name("puntos-venta.index");
    Route::post("/business/{business_id}/sucursales/{sucursal_id}/puntos-venta", [SucursalController::class, "store_punto_venta"])->name("puntos-venta.store_punto_venta");
    Route::get("/business/{business_id}/sucursales/{sucursal_id}/puntos-venta/{id}", [SucursalController::class, "edit_punto_venta"])->name("puntos-venta.edit");
    Route::put("/business/{business_id}/sucursales/{sucursal_id}/puntos-venta/{id}", [SucursalController::class, "update_punto_venta"])->name("puntos-venta.update_punto_venta");
    Route::delete("/business/{business_id}/sucursales/{sucursal_id}/puntos-venta/{id}", [SucursalController::class, "delete_punto_venta"])->name("puntos-venta.delete_punto_venta");

});