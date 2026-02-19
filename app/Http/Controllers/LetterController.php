<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function suratKeteranganP3H()
    {
        return view('letters.surat-keterangan-p3h');
    }

    public function suratPengantar()
    {
        return view('letters.surat-pengantar');
    }

    public function suratTugas()
    {
        return view('letters.surat-tugas');
    }
}
