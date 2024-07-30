<?php

use App\Http\Controllers\Api\V1\Authentication\AuthenticationController;
use App\Http\Controllers\Api\V1\File\VoiceController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->as('authentication.')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('verify', [AuthenticationController::class, 'verify'])->name('verify');
    Route::post('logout', [AuthenticationController::class, 'logout'])->middleware('auth:api-user')->name('logout');
});

Route::middleware('auth:api-user')->group(function () {
    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('current', [ProfileController::class, 'current'])->name('current');
        Route::post('update', [ProfileController::class, 'update'])->name('update');
    });
    Route::prefix('voices')->as('voices.')->group(function () {
        Route::post('upload', [VoiceController::class, 'upload'])->name('upload');

    });


});

