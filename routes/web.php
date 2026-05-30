<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;

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
    return view('dashboard');
})->name('dashboard');

Route::get('/daftar-surat', function () {
    return view('daftar-surat');
})->name('daftar-surat');

Route::get('/format-nomor-surat', function () {
    return view('format-nomor-surat');
})->name('format-nomor-surat');

Route::get('/riwayat-surat', function () {
    return view('riwayat-surat');
})->name('riwayat-surat');

Route::get('/pendamping', function () {
    return view('pendamping');
})->name('pendamping');

Route::get('/user', function () {
    return view('user');
})->name('user');

Route::get('/kepala-lp3h', function () {
    return view('kepala-lp3h');
})->name('kepala-lp3h');

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