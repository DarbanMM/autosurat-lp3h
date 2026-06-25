<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\PendampingController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/surat-keterangan-p3h', [LetterController::class, 'suratKeteranganP3H']);
Route::get('/surat-pengantar', [LetterController::class, 'suratPengantar']);
Route::get('/surat-tugas', [LetterController::class, 'suratTugas']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('/daftar-surat', function () {
    return view('admin.daftar-surat');
})->name('daftar-surat');

Route::get('/format-nomor-surat', function () {
    return view('admin.format-nomor-surat');
})->name('format-nomor-surat');

Route::get('/riwayat-surat', function () {
    return view('admin.riwayat-surat');
})->name('riwayat-surat');

Route::get('/pendamping', function () {
    return view('admin.pendamping');
})->name('pendamping');

Route::get('/user', function () {
    return view('admin.user');
})->name('user');

Route::get('/kepala-lp3h', function () {
    return view('admin.kepala-lp3h');
})->name('kepala-lp3h');

Route::get('/profil', function () {
    return view('user.profil');
})->name('profil');

Route::get('/buat-surat', function () {
    return view('user.buat-surat');
})->name('buat-surat');

Route::post('/login', function () {
    return redirect('/');
});

// Halaman Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Proses Login
Route::post('/login', function () {
    return redirect('/');
});

// Route logout untuk menangani tombol "Keluar" di sidebar
Route::post('/logout', function () {
    // Setelah logout, arahkan kembali ke halaman login
    return redirect('/login');
});

use App\Http\Controllers\UserController;

// Pendamping API
Route::get('/pendamping/data', [PendampingController::class, 'index'])->name('pendamping.data');
Route::post('/pendamping/import/prepare', [PendampingController::class, 'prepareImport'])->name('pendamping.import.prepare');
Route::post('/pendamping/import/{importId}/chunk', [PendampingController::class, 'importChunk'])->name('pendamping.import.chunk');
Route::post('/pendamping/import/async', [PendampingController::class, 'startAsyncImport'])->name('pendamping.import.async');
Route::get('/pendamping/import/{importId}/status', [PendampingController::class, 'importStatus'])->name('pendamping.import.status');
Route::post('/pendamping/import', [PendampingController::class, 'import'])->name('pendamping.import');
Route::get('/pendamping/download-template/{format}', [PendampingController::class, 'downloadTemplate'])->name('pendamping.download-template');

// User API
Route::get('/user/data', [UserController::class, 'index'])->name('user.data');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
Route::post('/user/sync', [UserController::class, 'sync'])->name('user.sync');
Route::post('/user/import/prepare', [UserController::class, 'prepareImport'])->name('user.import.prepare');
Route::post('/user/import/{importId}/chunk', [UserController::class, 'importChunk'])->name('user.import.chunk');
Route::get('/user/import/{importId}/status', [UserController::class, 'importStatus'])->name('user.import.status');
Route::get('/user/export/{format}', [UserController::class, 'export'])->name('user.export');
Route::get('/user/download-template/{format}', [UserController::class, 'downloadTemplate'])->name('user.download-template');

// Format Nomor Surat API
use App\Http\Controllers\FormatNomorSuratController;

Route::get('/format-nomor-surat/data', [FormatNomorSuratController::class, 'index'])->name('format-nomor-surat.data');
Route::post('/format-nomor-surat/store', [FormatNomorSuratController::class, 'store'])->name('format-nomor-surat.store');
Route::put('/format-nomor-surat/{id}', [FormatNomorSuratController::class, 'update'])->name('format-nomor-surat.update');
Route::delete('/format-nomor-surat/{id}', [FormatNomorSuratController::class, 'destroy'])->name('format-nomor-surat.destroy');

// Riwayat Surat API
use App\Http\Controllers\RiwayatSuratController;

Route::get('/riwayat-surat/data', [RiwayatSuratController::class, 'index'])->name('riwayat-surat.data');
Route::get('/riwayat-surat/export', [RiwayatSuratController::class, 'export'])->name('riwayat-surat.export');
