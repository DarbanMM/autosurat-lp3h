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

// Pendamping API
Route::get('/pendamping/data', [PendampingController::class, 'index'])->name('pendamping.data');
Route::post('/pendamping/import', [PendampingController::class, 'import'])->name('pendamping.import');
Route::get('/pendamping/download-template/{format}', [PendampingController::class, 'downloadTemplate'])->name('pendamping.download-template');
