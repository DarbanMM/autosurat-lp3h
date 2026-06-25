<?php

namespace App\Http\Controllers;

use App\Models\DataKetua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DataKetuaController extends Controller
{
    /**
     * Get the current Data Ketua.
     */
    public function index()
    {
        $ketua = DataKetua::first();

        if (!$ketua) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nip' => $ketua->nip,
                'nama' => $ketua->nama,
                'jabatan' => $ketua->jabatan,
                'barcode_ttd' => $ketua->barcode_ttd,
                // Add preview URL for the frontend
                'ttd_url' => $ketua->barcode_ttd ? asset('uploads/ttd/' . $ketua->barcode_ttd) : null,
            ],
        ]);
    }

    /**
     * Update Data Ketua.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
        ]);

        $ttdPreviewBase64 = $request->input('ttd_preview');
        $ttdName = $request->input('ttd_name');
        
        $ketua = DataKetua::first();
        $filename = $ketua ? $ketua->barcode_ttd : null;

        // If a new cropped image is provided
        if ($ttdPreviewBase64 && preg_match('/^data:image\/(\w+);base64,/', $ttdPreviewBase64, $type)) {
            $data = substr($ttdPreviewBase64, strpos($ttdPreviewBase64, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'svg+xml', 'svg'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format gambar tidak didukung.',
                ], 400);
            }

            // Decode base64
            $data = base64_decode($data);

            if ($data === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca file gambar.',
                ], 400);
            }

            // Make directory if not exists
            $uploadDir = public_path('uploads/ttd');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = $type === 'svg+xml' ? 'svg' : $type;
            $filename = 'ttd_' . time() . '.' . $extension;
            
            // Save file
            File::put($uploadDir . '/' . $filename, $data);

            // Delete old file if exists
            if ($ketua && $ketua->barcode_ttd) {
                $oldPath = $uploadDir . '/' . $ketua->barcode_ttd;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
        }

        // We only support 1 record. Just truncate and insert to handle NIP PK changes seamlessly.
        DataKetua::truncate();

        DataKetua::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'barcode_ttd' => $filename,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Kepala LP3H berhasil disimpan.',
        ]);
    }
}
