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

Route::get('/download/{file}', [AnimationController::class, 'download'])->name('animations.download')->middleware('auth');

Route::get('/dashboard', [AnimationController::class, 'index'])->name('dashboard')->middleware('auth');

Route::delete('/animations/{id}', [AnimationController::class, 'destroy'])->name('animations.destroy')->middleware('auth');
Route::delete('/animations/forget-deleted', [AnimationController::class, 'forgetDeleted'])->name('animations.forgetDeleted')->middleware('auth');
