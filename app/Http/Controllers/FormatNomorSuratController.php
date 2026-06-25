<?php

namespace App\Http\Controllers;

use App\Models\FormatNomorCounter;
use App\Models\FormatNomorSurat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class FormatNomorSuratController extends Controller
{
    /**
     * List all formats.
     */
    public function index()
    {
        $formats = FormatNomorSurat::orderBy('id_format_nomor')->get()->map(function (FormatNomorSurat $f) {
            return [
                'id' => $f->id_format_nomor,
                'parts' => $f->setting_surat ?? [],
                'reset_period' => $f->reset_period,
                'display_format' => $f->display_format,
            ];
        });

        return response()->json(['success' => true, 'data' => $formats]);
    }

    /**
     * Create a new format.
     */
    public function store(Request $request)
    {
        $validated = $this->validateFormat($request);

        try {
            $format = FormatNomorSurat::create([
                'setting_surat' => $validated['parts'],
                'reset_period' => $validated['reset_period'] ?? null,
                'display_format' => $validated['display_format'] ?? '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Format nomor surat berhasil ditambahkan.',
                'data' => [
                    'id' => $format->id_format_nomor,
                    'parts' => $format->setting_surat,
                    'reset_period' => $format->reset_period,
                    'display_format' => $format->display_format,
                ],
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan format: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing format.
     */
    public function update(Request $request, int $id)
    {
        $format = FormatNomorSurat::findOrFail($id);
        $validated = $this->validateFormat($request);

        try {
            $format->update([
                'setting_surat' => $validated['parts'],
                'reset_period' => $validated['reset_period'] ?? null,
                'display_format' => $validated['display_format'] ?? '',
            ]);

            // Reset counters when format is edited
            FormatNomorCounter::resetForFormat($id);

            return response()->json([
                'success' => true,
                'message' => 'Format nomor surat berhasil diperbarui. Counter nomor surat telah direset.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui format: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a format.
     */
    public function destroy(int $id)
    {
        $format = FormatNomorSurat::findOrFail($id);

        try {
            $format->delete();

            return response()->json([
                'success' => true,
                'message' => 'Format nomor surat berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus format: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate format request.
     */
    private function validateFormat(Request $request): array
    {
        $request->validate([
            'parts' => 'required|array|min:1',
            'parts.*.type' => 'required|string|in:separator,nomor,text,bulan,tahun',
            'parts.*.format' => 'required|string',
            'reset_period' => 'nullable|string|in:Mingguan,Bulanan,Tahunan',
            'display_format' => 'nullable|string|max:500',
        ]);

        return $request->only(['parts', 'reset_period', 'display_format']);
    }
}
