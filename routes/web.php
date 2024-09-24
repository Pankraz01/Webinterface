<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimationController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/upload', [AnimationController::class, 'showUploadForm'])->name('animations.upload.form')->middleware('auth');
Route::post('/upload', [AnimationController::class, 'upload'])->name('animations.upload')->middleware('auth');

Route::get('/dashboard', [AnimationController::class, 'index'])->name('dashboard')->middleware('auth');

