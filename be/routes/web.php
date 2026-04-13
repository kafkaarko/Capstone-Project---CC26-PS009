<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OCRController;


// Route::get('/dashboard', function () {
//     return view('dashboard.expense');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('dashboard.expense');
    });

    Route::get('/upload-page', [OCRController::class, 'index'])->name('upload.page');

    Route::post('/upload', [OCRController::class, 'upload'])->name('upload');

    Route::get('/dashboard-expense', [OCRController::class, 'dashboard'])->name('dashboard.expense');

    Route::post('/set-budget', [OCRController::class, 'setBudget'])->name('set.budget');

      Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// Route::get('/', [OCRController::class, 'index'])->middleware('auth');
// Route::post('/upload', [OCRController::class, 'upload'])->middleware('auth')->name('upload');
// Route::get('/dashboard-expense', [OCRController::class, 'dashboard'])->middleware('auth')->name('dashboard.expense');
// Route::post('/set-budget', [OCRController::class, 'setBudget'])->middleware('auth')->name('set.budget');
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
require __DIR__.'/auth.php';
