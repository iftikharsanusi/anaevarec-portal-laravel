<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AnalyticsController;

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
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::post('/analyze', [App\Http\Controllers\AnalyzeController::class, 'analyze'])->name('analyze');
    Route::get('/analysis/{id}', [App\Http\Controllers\AnalyzeController::class, 'show'])->name('analysis.show');
    Route::delete('/analysis/{id}', [HistoryController::class, 'destroy'])->name('analysis.destroy');
    Route::get('/test-screenshot', [App\Http\Controllers\AnalyzeController::class, 'testScreenshot'])->name('test.screenshot');
});
