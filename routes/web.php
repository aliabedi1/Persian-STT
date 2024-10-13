<?php

//use Illuminate\Support\Facades\Route;
//
//Route::get('/', function () {
//    return view('welcome');
//});


use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;


Route::get('/' , [HomeController::class,'index'])->name('home');
Route::get('/pwa' , [HomeController::class,'pwa'])->name('pwa');