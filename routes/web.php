<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\TanahController;
use App\Http\Middleware\VerifyAdminSession;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginAction'])->name('login.action');
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated Routes
Route::middleware([VerifyAdminSession::class])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Laporan (Reports) Routes
    Route::get('dashboard/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('dashboard/laporan/export-tanah', [LaporanController::class, 'exportTanah'])->name('laporan.export-tanah');
    Route::get('dashboard/laporan/print-tanah', [LaporanController::class, 'printTanah'])->name('laporan.print-tanah');
    Route::get('dashboard/laporan/export-rekap', [LaporanController::class, 'exportRekap'])->name('laporan.export-rekap');
    Route::get('dashboard/laporan/print-rekap', [LaporanController::class, 'printRekap'])->name('laporan.print-rekap');
    Route::get('dashboard/laporan/export-mutasi', [LaporanController::class, 'exportMutasi'])->name('laporan.export-mutasi');
    Route::get('dashboard/laporan/print-mutasi', [LaporanController::class, 'printMutasi'])->name('laporan.print-mutasi');

    // Kepemilikan & Mutasi Routes
    Route::get('dashboard/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('dashboard/mutasi/create', [MutasiController::class, 'create'])->name('mutasi.create');
    Route::post('dashboard/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
    Route::delete('dashboard/mutasi/{id}', [MutasiController::class, 'destroy'])->name('mutasi.destroy');

    // Pemilik Registry Routes
    Route::resource('dashboard/pemilik', PemilikController::class)->names([
        'index' => 'pemilik.index',
        'create' => 'pemilik.create',
        'store' => 'pemilik.store',
        'show' => 'pemilik.show',
        'edit' => 'pemilik.edit',
        'update' => 'pemilik.update',
        'destroy' => 'pemilik.destroy',
    ]);

    // Tanah Registry Routes
    Route::resource('dashboard/tanah', TanahController::class)->names([
        'index' => 'tanah.index',
        'create' => 'tanah.create',
        'store' => 'tanah.store',
        'show' => 'tanah.show',
        'edit' => 'tanah.edit',
        'update' => 'tanah.update',
        'destroy' => 'tanah.destroy',
    ]);

    // Master Data Group
    Route::prefix('dashboard/master-data')->group(function () {
        // Jenis Hak Tanah
        Route::get('/jenis-hak', [MasterDataController::class, 'jenisHakIndex'])->name('master-data.jenis-hak.index');
        Route::post('/jenis-hak', [MasterDataController::class, 'jenisHakStore'])->name('master-data.jenis-hak.store');
        Route::put('/jenis-hak/{id}', [MasterDataController::class, 'jenisHakUpdate'])->name('master-data.jenis-hak.update');
        Route::delete('/jenis-hak/{id}', [MasterDataController::class, 'jenisHakDestroy'])->name('master-data.jenis-hak.destroy');

        // Wilayah
        Route::get('/wilayah', [MasterDataController::class, 'wilayahIndex'])->name('master-data.wilayah.index');
        Route::post('/wilayah', [MasterDataController::class, 'wilayahStore'])->name('master-data.wilayah.store');
        Route::put('/wilayah/{id}', [MasterDataController::class, 'wilayahUpdate'])->name('master-data.wilayah.update');
        Route::delete('/wilayah/{id}', [MasterDataController::class, 'wilayahDestroy'])->name('master-data.wilayah.destroy');

        // Status Tanah
        Route::get('/status-tanah', [MasterDataController::class, 'statusTanahIndex'])->name('master-data.status-tanah.index');
        Route::post('/status-tanah', [MasterDataController::class, 'statusTanahStore'])->name('master-data.status-tanah.store');
        Route::put('/status-tanah/{id}', [MasterDataController::class, 'statusTanahUpdate'])->name('master-data.status-tanah.update');
        Route::delete('/status-tanah/{id}', [MasterDataController::class, 'statusTanahDestroy'])->name('master-data.status-tanah.destroy');
    });
});
