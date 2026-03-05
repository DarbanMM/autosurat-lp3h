<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/surat-keterangan-p3h', [LetterController::class, 'suratKeteranganP3H']);
Route::get('/surat-pengantar', [LetterController::class, 'suratPengantar']);
Route::get('/surat-tugas', [LetterController::class, 'suratTugas']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    return redirect('/');
});
