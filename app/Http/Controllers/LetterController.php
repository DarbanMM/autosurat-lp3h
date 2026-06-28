<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataKetua;
use App\Models\Pendamping;
use App\Models\Surat;
use App\Models\FormatNomorCounter;
use App\Models\RiwayatSurat;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LetterController extends Controller
{
    private function generateNomorSurat($formatNomor)
    {
        if (!$formatNomor || empty($formatNomor->setting_surat)) {
            return "000/DEFAULT/2026";
        }
        
        $resetPeriod = $formatNomor->reset_period ?? 'Tahunan';
        $nomorUrut = FormatNomorCounter::getNextNumber($formatNomor->id_format_nomor, $resetPeriod);
        
        $parts = $formatNomor->setting_surat;
        $nomorSuratString = "";
        
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        foreach ($parts as $part) {
            $type = $part['type'] ?? '';
            $format = $part['format'] ?? '';
            
            switch ($type) {
                case 'text':
                case 'separator':
                    $nomorSuratString .= $format;
                    break;
                case 'nomor':
                    $length = strlen($format);
                    $nomorSuratString .= str_pad($nomorUrut, $length, '0', STR_PAD_LEFT);
                    break;
                case 'bulan':
                    $nomorSuratString .= $romans[$currentMonth] ?? $format;
                    break;
                case 'tahun':
                    $nomorSuratString .= $currentYear;
                    break;
            }
        }
        
        return $nomorSuratString;
    }

    public function downloadSuratKeteranganP3H()
    {
        $user = Auth::user();
        $pendamping = Pendamping::where('no_registrasi', $user->username)->first();
        $ketua = DataKetua::first();
        $surat = Surat::with('formatNomorSurat')->where('id_surat', 1)->first(); // SK P3H is ID 1
        
        if (!$pendamping) {
            return back()->with('error', 'Data pendamping tidak ditemukan.');
        }
        
        $nomorSurat = $this->generateNomorSurat($surat ? $surat->formatNomorSurat : null);
        
        // Indonesian Date
        Carbon::setLocale('id');
        $tanggal = Carbon::now()->isoFormat('D MMMM Y');
        
        // Convert image to base64 for dompdf (so it doesn't try to fetch via HTTP or get blocked)
        $ttdBase64 = null;
        if ($ketua && $ketua->barcode_ttd) {
            $path = public_path('uploads/ttd/' . $ketua->barcode_ttd);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $ttdBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // Add to history
        RiwayatSurat::create([
            'tgl_dibuat' => now(),
            'created_at' => now(),
            'nomor_surat' => $nomorSurat,
            'nama_surat' => $surat ? $surat->nama_surat : 'Surat Keterangan Pendampingan (SK P3H)',
            'keterangan' => [
                'Nama Pendamping' => $pendamping->nama,
                'No Registrasi' => $pendamping->no_registrasi,
                'Ketua LP3H' => $ketua ? $ketua->nama : null
            ],
        ]);
        
        $pdf = Pdf::loadView('letters.surat-keterangan-p3h', compact(
            'nomorSurat', 'pendamping', 'ketua', 'tanggal', 'ttdBase64'
        ));
        
        // Set paper size A4
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Surat_Keterangan_P3H_' . str_replace('/', '_', $nomorSurat) . '.pdf');
    }

    public function downloadSuratPengantar(Request $request)
    {
        $user = Auth::user();
        $pendamping = Pendamping::where('no_registrasi', $user->username)->first();
        $ketua = DataKetua::first();
        $surat = Surat::with('formatNomorSurat')->where('id_surat', 2)->first(); // Surat Pengantar is ID 2
        
        if (!$pendamping) {
            return back()->with('error', 'Data pendamping tidak ditemukan.');
        }
        
        $tujuan_kepada = $request->input('tujuan_kepada', '');
        $daerah = $request->input('daerah', '');
        
        $nomorSurat = $this->generateNomorSurat($surat ? $surat->formatNomorSurat : null);
        
        Carbon::setLocale('id');
        $tanggal = Carbon::now()->isoFormat('D MMMM Y');
        
        $ttdBase64 = null;
        if ($ketua && $ketua->barcode_ttd) {
            $path = public_path('uploads/ttd/' . $ketua->barcode_ttd);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $ttdBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        RiwayatSurat::create([
            'tgl_dibuat' => now(),
            'created_at' => now(),
            'nomor_surat' => $nomorSurat,
            'nama_surat' => $surat ? $surat->nama_surat : 'Surat Pengantar Pendampingan',
            'keterangan' => [
                'Nama Pendamping' => $pendamping->nama,
                'Nomor Registrasi' => $pendamping->no_registrasi,
                'Tujuan Kepada' => $tujuan_kepada,
                'Daerah/Wilayah' => $daerah,
                'Ketua LP3H' => $ketua ? $ketua->nama : null
            ],
        ]);
        
        $pdf = Pdf::loadView('letters.surat-pengantar', compact(
            'nomorSurat', 'pendamping', 'ketua', 'tanggal', 'ttdBase64', 'tujuan_kepada', 'daerah'
        ));
        
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Surat_Pengantar_' . str_replace('/', '_', $nomorSurat) . '.pdf');
    }

    public function downloadSuratTugas(Request $request)
    {
        $user = Auth::user();
        $pendamping = Pendamping::where('no_registrasi', $user->username)->first();
        $ketua = DataKetua::first();
        $surat = Surat::with('formatNomorSurat')->where('id_surat', 3)->first(); // Surat Tugas is ID 3
        
        if (!$pendamping) {
            return back()->with('error', 'Data pendamping tidak ditemukan.');
        }
        
        $wilayah = $request->input('wilayah', '');
        $tanggal_mulai_raw = $request->input('tanggal_mulai', '');
        $tanggal_selesai_raw = $request->input('tanggal_selesai', '');
        
        Carbon::setLocale('id');
        $tanggal_mulai = $tanggal_mulai_raw ? Carbon::parse($tanggal_mulai_raw)->isoFormat('D MMMM Y') : '';
        $tanggal_selesai = $tanggal_selesai_raw ? Carbon::parse($tanggal_selesai_raw)->isoFormat('D MMMM Y') : '';
        $tanggal = Carbon::now()->isoFormat('D MMMM Y');
        
        $nomorSurat = $this->generateNomorSurat($surat ? $surat->formatNomorSurat : null);
        
        $ttdBase64 = null;
        if ($ketua && $ketua->barcode_ttd) {
            $path = public_path('uploads/ttd/' . $ketua->barcode_ttd);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $ttdBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        RiwayatSurat::create([
            'tgl_dibuat' => now(),
            'created_at' => now(),
            'nomor_surat' => $nomorSurat,
            'nama_surat' => $surat ? $surat->nama_surat : 'Surat Tugas Pendampingan Lapangan',
            'keterangan' => [
                'Ketua LP3H' => $ketua ? $ketua->nama : null,
                'Nama Pendamping' => $pendamping->nama,
                'No Registrasi' => $pendamping->no_registrasi,
                'Wilayah Penugasan' => $wilayah,
                'Masa Penugasan' => $tanggal_mulai . ' – ' . $tanggal_selesai,
            ],
        ]);
        
        $pdf = Pdf::loadView('letters.surat-tugas', compact(
            'nomorSurat', 'pendamping', 'ketua', 'tanggal', 'ttdBase64', 'wilayah', 'tanggal_mulai', 'tanggal_selesai'
        ));
        
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Surat_Tugas_' . str_replace('/', '_', $nomorSurat) . '.pdf');
    }
}
