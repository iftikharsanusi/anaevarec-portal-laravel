<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HistoryController;

// Guest Route (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Auth::routes();

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::post('/analyze', [App\Http\Controllers\AnalyzeController::class, 'analyze'])->name('analyze');
    Route::get('/analysis/{id}', [App\Http\Controllers\AnalyzeController::class, 'show'])->name('analysis.show');
});
