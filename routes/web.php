<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\DaftarSuratController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FormatNomorSuratController;
use App\Http\Controllers\RiwayatSuratController;
use App\Http\Controllers\DataKetuaController;

Route::get('/', function () {
    return redirect('/login');
});

// Public Letter generation routes
Route::get('/surat-keterangan-p3h', [LetterController::class, 'suratKeteranganP3H']);
Route::get('/surat-pengantar', [LetterController::class, 'suratPengantar']);
Route::get('/surat-tugas', [LetterController::class, 'suratTugas']);

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', function () {
            $totalDaftarSurat = \App\Models\Surat::count();
            $totalRiwayat = \App\Models\RiwayatSurat::count();
            $totalUser = \App\Models\User::count();
            return view('admin.dashboard', compact('totalDaftarSurat', 'totalRiwayat', 'totalUser'));
        })->name('dashboard');

        Route::get('/daftar-surat', function () { return view('admin.daftar-surat'); })->name('daftar-surat');
        Route::get('/format-nomor-surat', function () { return view('admin.format-nomor-surat'); })->name('format-nomor-surat');
        Route::get('/riwayat-surat', function () { return view('admin.riwayat-surat'); })->name('riwayat-surat');
        Route::get('/pendamping', function () { return view('admin.pendamping'); })->name('pendamping');
        Route::get('/user', function () { return view('admin.user'); })->name('user');
        Route::get('/kepala-lp3h', function () { return view('admin.kepala-lp3h'); })->name('kepala-lp3h');

        // Pendamping API
        Route::get('/pendamping/data', [PendampingController::class, 'index'])->name('pendamping.data');
        Route::post('/pendamping/import/prepare', [PendampingController::class, 'prepareImport'])->name('pendamping.import.prepare');
        Route::post('/pendamping/import/{importId}/chunk', [PendampingController::class, 'importChunk'])->name('pendamping.import.chunk');
        Route::post('/pendamping/import/async', [PendampingController::class, 'startAsyncImport'])->name('pendamping.import.async');
        Route::get('/pendamping/import/{importId}/status', [PendampingController::class, 'importStatus'])->name('pendamping.import.status');
        Route::post('/pendamping/import', [PendampingController::class, 'import'])->name('pendamping.import');
        Route::get('/pendamping/download-template/{format}', [PendampingController::class, 'downloadTemplate'])->name('pendamping.download-template');

        // Daftar Surat API
        Route::get('/daftar-surat/data', [DaftarSuratController::class, 'index'])->name('daftar-surat.data');
        Route::get('/daftar-surat/formats', [DaftarSuratController::class, 'formats'])->name('daftar-surat.formats');
        Route::post('/daftar-surat', [DaftarSuratController::class, 'store'])->name('daftar-surat.store');
        Route::put('/daftar-surat/{id}', [DaftarSuratController::class, 'update'])->name('daftar-surat.update');
        Route::delete('/daftar-surat/{id}', [DaftarSuratController::class, 'destroy'])->name('daftar-surat.destroy');

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
        Route::get('/format-nomor-surat/data', [FormatNomorSuratController::class, 'index'])->name('format-nomor-surat.data');
        Route::post('/format-nomor-surat/store', [FormatNomorSuratController::class, 'store'])->name('format-nomor-surat.store');
        Route::put('/format-nomor-surat/{id}', [FormatNomorSuratController::class, 'update'])->name('format-nomor-surat.update');
        Route::delete('/format-nomor-surat/{id}', [FormatNomorSuratController::class, 'destroy'])->name('format-nomor-surat.destroy');

        // Riwayat Surat API
        Route::get('/riwayat-surat/data', [RiwayatSuratController::class, 'index'])->name('riwayat-surat.data');
        Route::get('/riwayat-surat/export', [RiwayatSuratController::class, 'export'])->name('riwayat-surat.export');

        // Data Kepala LP3H API
        Route::get('/kepala-lp3h/data', [DataKetuaController::class, 'index'])->name('kepala-lp3h.data');
        Route::post('/kepala-lp3h/update', [DataKetuaController::class, 'update'])->name('kepala-lp3h.update');
    });

    // Regular User Routes
    Route::get('/profil', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $pendamping = \App\Models\Pendamping::where('no_registrasi', $user->username)->first();
        return view('user.profil', compact('pendamping'));
    })->name('profil');
    Route::get('/buat-surat', function () { return view('user.buat-surat'); })->name('buat-surat');
});
