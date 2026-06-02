<?php

namespace App\Http\Controllers;

use App\Models\Pendamping;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PendampingController extends Controller
{
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

    public function import(Request $request)
    {
        // Set timeout for large imports
        set_time_limit(600);
        ini_set('memory_limit', '512M');
        DB::connection()->disableQueryLog();

        $request->validate([
            // CSV uploads are often detected as text/plain; validate by extension instead.
            'file' => 'required|file|extensions:csv,xlsx,xls|max:50240',
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->path();
            $fileName = $file->getClientOriginalName();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Handle Excel files by converting to CSV
            if (in_array($extension, ['xlsx', 'xls'])) {
                $rows = $this->readExcelFile($filePath);
            } else {
                $rows = $this->readCsvFile($filePath);
            }

            if (empty($rows)) {
                return response()->json(['success' => false, 'message' => 'File kosong'], 400);
            }

            $rawHeaders = array_shift($rows);
            $requiredColumns = [
                'no_registrasi', 'id_pendamping', 'id_lembaga', 'no_pendaftaran',
                'tgl_berlaku', 'nama', 'alamat', 'kode_pos', 'kecamatan', 'kabupaten',
                'provinsi', 'no_hp', 'tempat_lahir', 'tgl_lahir', 'nik', 'pendidikan',
                'universitas', 'status', 'nama_lembaga', 'sumber_data', 'jumlah_pu',
                'pekerjaan', 'pekerjaan_lain', 'asal_unit_kerja', 'pns', 'pns_golongan',
            ];

            $headerIndexes = $this->buildHeaderIndexes($rawHeaders);
            $missingColumns = array_values(array_diff($requiredColumns, array_keys($headerIndexes)));
            if ($missingColumns !== []) {
                return response()->json([
                    'success' => false,
                    'message' => "Kolom tidak ditemukan: " . implode(', ', $missingColumns) . ". Gunakan template CSV dari tombol Download Template.",
                ], 400);
            }

            $columnCount = count($rawHeaders);
            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $rowIndex => $row) {
                $row = array_pad($row, $columnCount, '');

                if ($this->isRowEmpty($row)) {
                    continue;
                }

                $built = $this->buildImportRow($row, $headerIndexes, $requiredColumns);
                if ($built['error'] !== null) {
                    $skipped++;
                    $errors[] = 'Baris ' . ($rowIndex + 2) . ': ' . $built['error'];
                    continue;
                }

                try {
                    $this->upsertPendampingWithRetry($built['data']);
                    $imported++;
                } catch (QueryException $e) {
                    $skipped++;
                    $errors[] = 'Baris ' . ($rowIndex + 2) . ': ' . $this->friendlyQueryError($e);
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = 'Baris ' . ($rowIndex + 2) . ': ' . $e->getMessage();
                }
            }

            $message = "Berhasil import {$imported} data pendamping.";
            if ($skipped > 0) {
                $message .= " {$skipped} baris dilewatkan.";
            }

            return response()->json([
                'success' => $imported > 0,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => array_slice($errors, 0, 10),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @return array{data: array<string, mixed>|null, error: string|null}
     */
    private function buildImportRow(array $row, array $headerIndexes, array $requiredColumns): array
    {
        $data = [];

        foreach ($requiredColumns as $col) {
            $value = $row[$headerIndexes[$col]] ?? null;
            $value = trim((string) $value);

            if ($col === 'pns') {
                continue;
            }

            if (in_array($col, ['nik', 'no_hp', 'no_registrasi', 'id_pendamping', 'id_lembaga', 'no_pendaftaran'], true)
                && preg_match('/^[0-9.E+\-]+$/i', $value)) {
                $value = $this->normalizeNumericString($value);
            }

            if (in_array($col, ['tgl_berlaku', 'tgl_lahir'], true)) {
                $value = $this->convertToDate($value);
            } elseif ($col === 'jumlah_pu') {
                $value = is_numeric($value) ? (int) $value : null;
            }

            $data[$col] = $value;
        }

        if ($data['no_registrasi'] === '' || $data['no_registrasi'] === '0') {
            return [
                'data' => null,
                'error' => 'no_registrasi kosong (periksa pemisah kolom , atau ; di CSV).',
            ];
        }

        if ($data['id_pendamping'] === '') {
            return [
                'data' => null,
                'error' => 'id_pendamping wajib diisi.',
            ];
        }

        $pnsCell = trim((string) ($row[$headerIndexes['pns']] ?? ''));
        $data['pns'] = $this->resolvePnsBoolean($pnsCell, $data['pns_golongan'] ?? '');

        return ['data' => $data, 'error' => null];
    }

    private function buildHeaderIndexes(?array $headers): array
    {
        if ($headers === null) {
            return [];
        }

        $indexes = [];
        foreach ($headers as $index => $header) {
            $canonical = $this->canonicalHeaderName((string) $header);
            if ($canonical !== '' && ! isset($indexes[$canonical])) {
                $indexes[$canonical] = $index;
            }
        }

        return $indexes;
    }

    private function canonicalHeaderName(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
        $header = strtolower(trim($header));
        $header = preg_replace('/[\s\-]+/', '_', $header);
        $header = preg_replace('/[^a-z0-9_]/', '', $header);

        $aliases = [
            'no_reg' => 'no_registrasi',
            'noregrasi' => 'no_registrasi',
            'no_registrasi_' => 'no_registrasi',
            'id_pendamping_' => 'id_pendamping',
            'idpendamping' => 'id_pendamping',
            'kodepos' => 'kode_pos',
            'nohp' => 'no_hp',
            'no_hp_' => 'no_hp',
            'tglberlaku' => 'tgl_berlaku',
            'tgllahir' => 'tgl_lahir',
            'nama_lembaga_' => 'nama_lembaga',
            'asalunitkerja' => 'asal_unit_kerja',
            'pns_golongan_' => 'pns_golongan',
            'pnsgolongan' => 'pns_golongan',
        ];

        return $aliases[$header] ?? $header;
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function detectDelimiter(string $line): string
    {
        $counts = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];

        arsort($counts);
        $delimiter = array_key_first($counts);

        return ($counts[$delimiter] ?? 0) > 0 ? $delimiter : ',';
    }

    private function readCsvFile($filePath)
    {
        $rows = [];
        $firstLine = @file_get_contents($filePath, false, null, 0, 8192);

        if ($firstLine === false) {
            return [];
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $delimiter = $this->detectDelimiter(strtok($firstLine, "\r\n"));

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $this->expandMergedCsvRow($row, $delimiter);
            }
            fclose($handle);
        }

        return $rows;
    }

    private function expandMergedCsvRow(array $row, string $delimiter): array
    {
        if (count($row) !== 1) {
            return $row;
        }

        $line = trim((string) $row[0]);
        if ($line === '' || ! str_contains($line, $delimiter)) {
            return $row;
        }

        if (str_starts_with($line, '"') && str_ends_with($line, '"')) {
            $line = substr($line, 1, -1);
            $line = str_replace('""', '"', $line);
        }

        return str_getcsv($line, $delimiter);
    }

    private function normalizeNumericString(string $value): string
    {
        if (stripos($value, 'e') !== false) {
            return sprintf('%.0f', (float) $value);
        }

        return $value;
    }

    private function resolvePnsBoolean(string $pnsCell, string $pnsGolongan): bool
    {
        if ($pnsCell !== '') {
            return $this->convertToBoolean($pnsCell);
        }

        return trim($pnsGolongan) !== '' && trim($pnsGolongan) !== '0';
    }

    private function upsertPendampingWithRetry(array $data): void
    {
        try {
            $this->upsertPendamping($data);
        } catch (QueryException $e) {
            if (! str_contains($e->getMessage(), 'pendamping_nik_unique')) {
                throw $e;
            }

            // Excel scientific notation often collapses different NIKs to the same number.
            unset($data['nik']);
            $this->upsertPendamping($data);
        }
    }

    private function upsertPendamping(array $data): void
    {
        $pns = (bool) ($data['pns'] ?? false);
        unset($data['pns']);

        $record = Pendamping::updateOrCreate(
            ['no_registrasi' => $data['no_registrasi']],
            $data
        );

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::table('pendamping')
                ->where('no_registrasi', $record->no_registrasi)
                ->update(['pns' => DB::raw($pns ? 'TRUE' : 'FALSE')]);

            return;
        }

        $record->pns = $pns;
        $record->save();
    }

    private function friendlyQueryError(QueryException $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'pendamping_nik_unique')) {
            return 'NIK sudah digunakan oleh data pendamping lain.';
        }

        if (str_contains($message, 'pendamping_id_pendamping_unique')) {
            return 'ID pendamping sudah terdaftar.';
        }

        if (str_contains($message, 'Datatype mismatch') && str_contains($message, 'pns')) {
            return 'Nilai kolom PNS tidak valid (gunakan 0/1, true/false, atau kosong).';
        }

        return $message;
    }

    private function readExcelFile($filePath)
    {
        $rows = [];
        try {
            // Open Excel file as ZIP
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === TRUE) {
                // Read the shared strings file
                $strings = [];
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $xmlString = $zip->getFromName('xl/sharedStrings.xml');
                    $xml = simplexml_load_string($xmlString);
                    foreach ($xml->si as $si) {
                        $strings[] = (string)$si->t;
                    }
                }

                // Read the worksheet
                $worksheetFile = 'xl/worksheets/sheet1.xml';
                if ($zip->locateName($worksheetFile) !== false) {
                    $xmlSheet = $zip->getFromName($worksheetFile);
                    $xml = simplexml_load_string($xmlSheet);

                    foreach ($xml->sheetData->row as $row) {
                        $rowData = [];
                        foreach ($row->c as $cell) {
                            $value = '';
                            if (isset($cell->v)) {
                                $cellType = (string)$cell['t'];
                                if ($cellType === 's') {
                                    // String reference
                                    $value = $strings[(int)$cell->v] ?? '';
                                } else {
                                    // Direct value
                                    $value = (string)$cell->v;
                                }
                            }
                            $rowData[] = $value;
                        }
                        if (!empty(array_filter($rowData))) {
                            $rows[] = $rowData;
                        }
                    }
                }
                $zip->close();
            }
        } catch (\Exception $e) {
            // Fallback: Try to read as CSV if Excel reading fails
            return $this->readCsvFile($filePath);
        }

        return $rows;
    }

    private function convertToBoolean($value)
    {
        if (is_bool($value)) return $value;
        if (is_null($value) || $value === '') return false;

        $trueValues = ['1', 'true', 'yes', 'y', 'checked', 'on'];
        return in_array(strtolower((string)$value), $trueValues);
    }

    private function convertToDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $baseDate = new \DateTime('1899-12-30');
                $baseDate->modify('+' . (int) $value . ' days');

                return $baseDate->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
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
            'asal_unit_kerja', 'pns', 'pns_golongan'
        ];

        if ($format === 'csv') {
            $filename = 'template_pendamping.csv';
            $headers = ['Content-Type' => 'text/csv'];
        } else {
            $filename = 'template_pendamping.xlsx';
            $headers = ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        }

        return response()->stream(function () use ($columns, $format) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $columns);
            fputcsv($handle, array_fill(0, count($columns), ''));

            fclose($handle);
        }, 200, array_merge($headers, [
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ]));
    }
}

