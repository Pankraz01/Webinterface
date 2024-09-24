<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimationController;


Auth::routes();

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});


Route::get('/upload', [AnimationController::class, 'showUploadForm'])->name('animations.upload.form')->middleware('auth');
Route::post('/upload', [AnimationController::class, 'upload'])->name('animations.upload')->middleware('auth');

Route::get('/download/{file}', [AnimationController::class, 'download'])->name('animations.download')->middleware('auth');

Route::get('/dashboard', [AnimationController::class, 'index'])->name('dashboard')->middleware('auth');

Route::delete('/animations/{id}', [AnimationController::class, 'destroy'])->name('animations.destroy')->middleware('auth');
Route::post('/animations/forget-deleted', [AnimationController::class, 'forgetDeleted'])->name('animations.forgetDeleted')->middleware('auth');
