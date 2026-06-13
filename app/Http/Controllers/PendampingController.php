<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPendampingImportJob;
use App\Models\Pendamping;
use App\Models\PendampingImport;
use App\Services\PendampingImportService;
use Illuminate\Http\Request;

class PendampingController extends Controller
{
    public function __construct(
        private PendampingImportService $importService
    ) {}

    public function index(Request $request)
    {
        $query = Pendamping::query()->orderBy('no_registrasi');

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('no_registrasi', 'like', '%'.$search.'%');
            });
        }

        $data = $query->get()->map(fn (Pendamping $pendamping) => $this->formatPendampingForJson($pendamping));

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function prepareImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:51200',
        ]);

        try {
            $import = $this->importService->prepareFromUpload($request->file('file'));

            return response()->json([
                'success' => true,
                'import_id' => $import->id,
                'total_rows' => $import->total_rows,
                'chunk_size' => $this->importService->chunkSize(),
                'message' => "File siap diimport ({$import->total_rows} baris).",
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function importChunk(Request $request, string $importId)
    {
        $request->validate([
            'offset' => 'required|integer|min:0',
        ]);

        $import = PendampingImport::findOrFail($importId);

        if ($import->isFinished()) {
            return response()->json(array_merge(
                $this->importService->statusPayload($import),
                ['success' => $import->status === 'completed']
            ));
        }

        try {
            $result = $this->importService->processChunk(
                $import,
                (int) $request->input('offset'),
                $this->importService->chunkSize()
            );

            return response()->json(array_merge([
                'success' => true,
                'import_id' => $import->id,
            ], $result));
        } catch (\Exception $e) {
            $import->update([
                'status' => 'failed',
                'message' => 'Error: '.$e->getMessage(),
                'completed_at' => now(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function startAsyncImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:51200',
        ]);

        try {
            $import = $this->importService->prepareFromUpload($request->file('file'));
            $import->update(['status' => 'pending']);
            ProcessPendampingImportJob::dispatch($import->id);

            return response()->json([
                'success' => true,
                'import_id' => $import->id,
                'total_rows' => $import->total_rows,
                'message' => 'Import dijalankan di background. Anda dapat menutup halaman ini.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function importStatus(string $importId)
    {
        $import = PendampingImport::findOrFail($importId);

        return response()->json(array_merge(
            $this->importService->statusPayload($import),
            ['success' => $import->status !== 'failed']
        ));
    }

    /** @deprecated Use prepareImport + importChunk or startAsyncImport */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:51200',
        ]);

        try {
            $import = $this->importService->prepareFromUpload($request->file('file'));
            $offset = 0;
            $chunkSize = $this->importService->chunkSize();

            while ($offset < $import->total_rows) {
                $this->importService->processChunk($import, $offset, $chunkSize);
                $import->refresh();
                $offset += $chunkSize;
            }

            $import->refresh();

            return response()->json([
                'success' => $import->imported_count > 0,
                'message' => $import->message,
                'imported' => $import->imported_count,
                'skipped' => $import->skipped_count,
                'errors' => array_slice($import->errors ?? [], 0, 10),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    private function formatPendampingForJson(Pendamping $pendamping): array
    {
        return [
            'no_registrasi' => $pendamping->no_registrasi,
            'id_pendamping' => $pendamping->id_pendamping,
            'id_lembaga' => $pendamping->id_lembaga,
            'no_pendaftaran' => $pendamping->no_pendaftaran,
            'tgl_berlaku' => $pendamping->tgl_berlaku?->format('Y-m-d'),
            'nama' => $pendamping->nama,
            'alamat' => $pendamping->alamat,
            'kode_pos' => $pendamping->kode_pos,
            'kecamatan' => $pendamping->kecamatan,
            'kabupaten' => $pendamping->kabupaten,
            'provinsi' => $pendamping->provinsi,
            'no_hp' => $pendamping->no_hp,
            'tempat_lahir' => $pendamping->tempat_lahir,
            'tgl_lahir' => $pendamping->tgl_lahir?->format('Y-m-d'),
            'nik' => $pendamping->nik,
            'pendidikan' => $pendamping->pendidikan,
            'universitas' => $pendamping->universitas,
            'status' => $pendamping->status,
            'nama_lembaga' => $pendamping->nama_lembaga,
            'sumber_data' => $pendamping->sumber_data,
            'jumlah_pu' => $pendamping->jumlah_pu,
            'pekerjaan' => $pendamping->pekerjaan,
            'pekerjaan_lain' => $pendamping->pekerjaan_lain,
            'asal_unit_kerja' => $pendamping->asal_unit_kerja,
            'pns' => (bool) $pendamping->pns,
            'pns_golongan' => $pendamping->pns_golongan,
        ];
    }

    public function downloadTemplate($format = 'csv')
    {
        $columns = [
            'id_pendamping', 'id_lembaga', 'no_pendaftaran', 'no_registrasi', 'tgl_berlaku',
            'nama', 'alamat', 'kode_pos', 'kecamatan', 'kabupaten', 'provinsi', 'no_hp',
            'tempat_lahir', 'tgl_lahir', 'nik', 'pendidikan', 'universitas', 'status',
            'nama_lembaga', 'sumber_data', 'jumlah_pu', 'pekerjaan', 'pekerjaan_lain',
            'asal_unit_kerja', 'pns', 'pns_golongan',
        ];

        if ($format === 'csv') {
            $filename = 'template_pendamping.csv';
            $headers = ['Content-Type' => 'text/csv'];
        } else {
            $filename = 'template_pendamping.xlsx';
            $headers = ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        }

        return response()->stream(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fputcsv($handle, array_fill(0, count($columns), ''));
            fclose($handle);
        }, 200, array_merge($headers, [
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]));
    }
}
