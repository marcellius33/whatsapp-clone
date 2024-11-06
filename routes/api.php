<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(static function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(static function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('profile', [AuthController::class, 'profile'])->name('auth.profile');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
        Route::patch('update-profile', [AuthController::class, 'updateProfile'])->name('auth.update-profile');
    });
});
