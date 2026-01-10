<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;

Route::redirect('/', '/imports');

Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
Route::get('/imports/create', [ImportController::class, 'create'])->name('imports.create');
Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');

Route::get('/reports/schools', [ReportController::class, 'schools'])->name('reports.schools');
Route::get('/reports/schools/export', [ReportController::class, 'exportSchools'])->name('reports.schools.export');
