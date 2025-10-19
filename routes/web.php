<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanCatatanHarianController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/laporan-catatan/export', [LaporanCatatanHarianController::class, 'exportExcel'])->name('export.laporan.catatan');
Route::get('/laporan-catatan/export/pdf', [LaporanCatatanHarianController::class, 'exportPdf'])->name('export.laporan.catatan.pdf');
