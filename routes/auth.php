<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware(["web"])->group(function () {
    Route::get('/', [LoginController::class, "index"])->name('index');
    Route::get('/login', [LoginController::class, "index"])->name('login');
    Route::get("/reset-password", [LoginController::class, "resetPassword"])->name('reset-password');

    Route::post("/validate", [AuthController::class, "login"])->name('validate');
    Route::post("/logout", [AuthController::class, "logout"])->name('logout');
    
    Route::get("/reset-password", [AuthController::class, "resetPassword"])->name('reset-password');
    Route::post("/send-email-reset-password", [AuthController::class, "sendEmailResetPassword"])->name('send-email-reset-password');
    Route::get("/password-change", [AuthController::class, "showChangePasswordForm"])->name("password.change");
    Route::post("/password-change", [AuthController::class, "changePassword"])->name("password.change.post");
});
