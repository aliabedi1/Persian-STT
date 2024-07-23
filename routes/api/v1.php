<?php

use App\Http\Controllers\Api\V1\Authentication\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->as('authentication')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login'])->name('login');
});