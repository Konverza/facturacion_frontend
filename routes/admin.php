<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlansController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin'])->prefix("admin")->name("admin.")->group(function () {
    Route::get("/", [DashboardController::class, "index"])->name('index');
    Route::get("/dashboard", [DashboardController::class, "index"])->name('dashboard');
    Route::resource("/business", BusinessController::class);
    Route::resource("/plans", PlansController::class);
    Route::resource("/users", UserController::class);
    Route::prefix("configuration")->name("configuration.")->group(function () {
        Route::get("/", [ConfigurationController::class, "index"])->name("index");
    });
    Route::get("/get-municipios", [BusinessController::class, "getMunicipios"])->name("get-municipios");
});