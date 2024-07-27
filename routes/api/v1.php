<?php

use App\Http\Controllers\Api\V1\Authentication\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->as('authentication')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('verify', [AuthenticationController::class, 'verify'])->name('verify');
    Route::post('logout', [AuthenticationController::class, 'logout'])->middleware('auth:api-user')->name('logout');

});

Route::middleware('auth:api-user')->group(function () {


});

