<?php

use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MapController;
use App\Http\Controllers\Web\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
Route::get('/map', [MapController::class, 'index'])->name('map');
Route::get('/about', [AboutController::class, 'index'])->name('about');
