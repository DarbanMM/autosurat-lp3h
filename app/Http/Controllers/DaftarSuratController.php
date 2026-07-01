<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\FormatNomorSurat;
use Illuminate\Http\Request;

class DaftarSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::with('formatNomorSurat')->orderBy('id_surat', 'desc');

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_surat) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(keterangan) like ?', ["%{$search}%"])
                  ->orWhereHas('formatNomorSurat', function ($qFormat) use ($search) {
                      $qFormat->whereRaw('LOWER(display_format) like ?', ["%{$search}%"]);
                  });
            });
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ]);
    }

    public function formats()
    {
        // Get list of all formats to show in the dropdown
        $formats = FormatNomorSurat::orderBy('id_format_nomor', 'desc')->get(['id_format_nomor', 'display_format']);
        return response()->json([
            'success' => true,
            'data' => $formats,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'id_format_surat' => 'nullable|exists:format_nomor_surat,id_format_nomor',
            'keterangan' => 'nullable|string',
        ]);

        $surat = Surat::create([
            'nama_surat' => $request->nama_surat,
            'id_format_surat' => $request->id_format_surat,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil ditambahkan',
            'data' => $surat,
        ]);
    }

    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);

        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'id_format_surat' => 'nullable|exists:format_nomor_surat,id_format_nomor',
            'keterangan' => 'nullable|string',
        ]);

        $surat->update([
            'nama_surat' => $request->nama_surat,
            'id_format_surat' => $request->id_format_surat,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil diperbarui',
            'data' => $surat,
        ]);
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);
        $surat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil dihapus',
        ]);
    }
}
