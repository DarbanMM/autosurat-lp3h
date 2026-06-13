<?php

namespace App\Services;

use App\Models\PendampingImport;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PendampingImportService
{
    public const REQUIRED_COLUMNS = [
        'no_registrasi', 'id_pendamping', 'id_lembaga', 'no_pendaftaran',
        'tgl_berlaku', 'nama', 'alamat', 'kode_pos', 'kecamatan', 'kabupaten',
        'provinsi', 'no_hp', 'tempat_lahir', 'tgl_lahir', 'nik', 'pendidikan',
        'universitas', 'status', 'nama_lembaga', 'sumber_data', 'jumlah_pu',
        'pekerjaan', 'pekerjaan_lain', 'asal_unit_kerja', 'pns', 'pns_golongan',
    ];

    private const UPSERT_UPDATE_COLUMNS = [
        'id_pendamping', 'id_lembaga', 'no_pendaftaran', 'tgl_berlaku', 'nama',
        'alamat', 'kode_pos', 'kecamatan', 'kabupaten', 'provinsi', 'no_hp',
        'tempat_lahir', 'tgl_lahir', 'nik', 'pendidikan', 'universitas', 'status',
        'nama_lembaga', 'sumber_data', 'jumlah_pu', 'pekerjaan', 'pekerjaan_lain',
        'asal_unit_kerja', 'pns', 'pns_golongan', 'updated_at',
    ];

    private const CHUNK_SIZE = 150;

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    public function prepareFromUpload(UploadedFile $file): PendampingImport
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['csv', 'xlsx', 'xls'], true)) {
            throw new \InvalidArgumentException('Format file harus CSV atau Excel.');
        }

        $importId = (string) Str::uuid();
        $storedPath = 'imports/'.$importId.'.'.$extension;
        Storage::disk('local')->putFileAs('imports', $file, $importId.'.'.$extension);
        $absolutePath = Storage::disk('local')->path($storedPath);

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $csvPath = Storage::disk('local')->path('imports/'.$importId.'.csv');
            $this->convertExcelToCsv($absolutePath, $csvPath);
            Storage::disk('local')->delete($storedPath);
            $storedPath = 'imports/'.$importId.'.csv';
            $absolutePath = $csvPath;
        }

        $delimiter = $this->detectDelimiterFromFile($absolutePath);
        [$headerMap, $columnCount] = $this->readHeader($absolutePath, $delimiter);
        $missing = array_values(array_diff(self::REQUIRED_COLUMNS, array_keys($headerMap)));
        if ($missing !== []) {
            Storage::disk('local')->delete($storedPath);
            throw new \InvalidArgumentException(
                'Kolom tidak ditemukan: '.implode(', ', $missing).'. Gunakan template CSV.'
            );
        }

        $totalRows = $this->countDataRows($absolutePath, $delimiter);

        return PendampingImport::create([
            'id' => $importId,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'delimiter' => $delimiter,
            'header_map' => $headerMap,
            'column_count' => $columnCount,
            'total_rows' => $totalRows,
            'status' => 'pending',
        ]);
    }

    public function prepareFromPath(string $absolutePath, ?string $originalFilename = null): PendampingImport
    {
        if (! is_file($absolutePath)) {
            throw new \InvalidArgumentException("File tidak ditemukan: {$absolutePath}");
        }

        $importId = (string) Str::uuid();
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $storedPath = 'imports/'.$importId.'.'.$extension;
        Storage::disk('local')->put($storedPath, file_get_contents($absolutePath));
        $storedAbsolute = Storage::disk('local')->path($storedPath);

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $csvPath = Storage::disk('local')->path('imports/'.$importId.'.csv');
            $this->convertExcelToCsv($storedAbsolute, $csvPath);
            Storage::disk('local')->delete($storedPath);
            $storedPath = 'imports/'.$importId.'.csv';
            $storedAbsolute = $csvPath;
        }

        $delimiter = $this->detectDelimiterFromFile($storedAbsolute);
        [$headerMap, $columnCount] = $this->readHeader($storedAbsolute, $delimiter);
        $missing = array_values(array_diff(self::REQUIRED_COLUMNS, array_keys($headerMap)));
        if ($missing !== []) {
            Storage::disk('local')->delete($storedPath);
            throw new \InvalidArgumentException(
                'Kolom tidak ditemukan: '.implode(', ', $missing)
            );
        }

        return PendampingImport::create([
            'id' => $importId,
            'original_filename' => $originalFilename ?? basename($absolutePath),
            'file_path' => $storedPath,
            'delimiter' => $delimiter,
            'header_map' => $headerMap,
            'column_count' => $columnCount,
            'total_rows' => $this->countDataRows($storedAbsolute, $delimiter),
            'status' => 'pending',
        ]);
    }

    public function processChunk(PendampingImport $import, int $offset, ?int $limit = null): array
    {
        $limit = $limit ?? self::CHUNK_SIZE;
        $import->update([
            'status' => 'processing',
            'started_at' => $import->started_at ?? now(),
        ]);

        $path = Storage::disk('local')->path($import->file_path);
        $rows = $this->readDataRows($path, $import->delimiter, $offset, $limit);
        $result = $this->processRows($import, $rows, $offset + 2, $offset + count($rows));

        $import->refresh();
        $finished = $import->processed_rows >= $import->total_rows;

        if ($finished) {
            $import->update([
                'status' => 'completed',
                'completed_at' => now(),
                'message' => "Berhasil import {$import->imported_count} data pendamping."
                    .($import->skipped_count > 0 ? " {$import->skipped_count} baris dilewatkan." : ''),
            ]);
        }

        return array_merge($result, [
            'finished' => $finished,
            'processed_rows' => $import->processed_rows,
            'total_rows' => $import->total_rows,
            'imported_count' => $import->imported_count,
            'skipped_count' => $import->skipped_count,
            'progress_percent' => $import->progressPercent(),
        ]);
    }

    public function processAll(PendampingImport $import, ?int $chunkSize = null): void
    {
        $chunkSize = $chunkSize ?? self::CHUNK_SIZE;
        $import->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $offset = 0;
        while ($offset < $import->total_rows) {
            $this->processChunk($import, $offset, $chunkSize);
            $import->refresh();
            $offset += $chunkSize;
        }
    }

    public function statusPayload(PendampingImport $import): array
    {
        return [
            'success' => true,
            'import_id' => $import->id,
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'imported_count' => $import->imported_count,
            'skipped_count' => $import->skipped_count,
            'progress_percent' => $import->progressPercent(),
            'message' => $import->message,
            'errors' => array_slice($import->errors ?? [], 0, 10),
            'finished' => $import->isFinished(),
        ];
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function processRows(PendampingImport $import, array $rows, int $startLineNumber, int $processedRows): array
    {
        DB::connection()->disableQueryLog();

        $headerMap = $import->header_map ?? [];
        $columnCount = $import->column_count;
        $validRows = [];
        $rowLineNumbers = [];
        $skipped = 0;
        $errors = $import->errors ?? [];

        foreach ($rows as $index => $row) {
            $row = array_pad($row, $columnCount, '');
            if ($this->isRowEmpty($row)) {
                continue;
            }

            $lineNumber = $startLineNumber + $index;
            $built = $this->buildImportRow($row, $headerMap, self::REQUIRED_COLUMNS);
            if ($built['error'] !== null) {
                $skipped++;
                $errors[] = 'Baris '.$lineNumber.': '.$built['error'];
                continue;
            }

            $validRows[] = $this->toDatabaseRow($built['data']);
            $rowLineNumbers[] = $lineNumber;
        }

        $imported = 0;
        if ($validRows !== []) {
            $bulkResult = $this->bulkUpsert($validRows, $rowLineNumbers);
            $imported = $bulkResult['imported'];
            $skipped += $bulkResult['skipped'];
            $errors = array_merge($errors, $bulkResult['errors']);
        }

        $import->update([
            'processed_rows' => min($import->total_rows, $processedRows),
            'imported_count' => $import->imported_count + $imported,
            'skipped_count' => $import->skipped_count + $skipped,
            'errors' => array_slice($errors, -50),
        ]);

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => array_slice($errors, 0, 10),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, int>  $lineNumbers
     */
    private function bulkUpsert(array $rows, array $lineNumbers): array
    {
        $now = now()->toDateTimeString();
        $prepared = [];

        foreach ($rows as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $row['pns'] = (bool) ($row['pns'] ?? false);
            $prepared[] = $row;
        }

        try {
            DB::table('pendamping')->upsert(
                $prepared,
                ['no_registrasi'],
                self::UPSERT_UPDATE_COLUMNS
            );

            return ['imported' => count($prepared), 'skipped' => 0, 'errors' => []];
        } catch (QueryException $e) {
            return $this->bulkUpsertIndividually($prepared, $lineNumbers);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, int>  $lineNumbers
     */
    private function bulkUpsertIndividually(array $rows, array $lineNumbers): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $line = $lineNumbers[$i] ?? ($i + 2);
            try {
                DB::table('pendamping')->upsert([$row], ['no_registrasi'], self::UPSERT_UPDATE_COLUMNS);
                $imported++;
            } catch (QueryException $e) {
                if (str_contains($e->getMessage(), 'pendamping_nik_unique')) {
                    unset($row['nik']);
                    try {
                        DB::table('pendamping')->upsert([$row], ['no_registrasi'], self::UPSERT_UPDATE_COLUMNS);
                        $imported++;
                        continue;
                    } catch (QueryException $inner) {
                        $errors[] = 'Baris '.$line.': '.$this->friendlyQueryError($inner);
                    }
                } else {
                    $errors[] = 'Baris '.$line.': '.$this->friendlyQueryError($e);
                }
                $skipped++;
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function toDatabaseRow(array $data): array
    {
        $emptyToNull = [
            'id_lembaga', 'no_pendaftaran', 'tgl_berlaku', 'alamat', 'kode_pos',
            'kecamatan', 'kabupaten', 'provinsi', 'no_hp', 'tempat_lahir', 'tgl_lahir',
            'nik', 'pendidikan', 'universitas', 'status', 'nama_lembaga', 'sumber_data',
            'pekerjaan', 'pekerjaan_lain', 'asal_unit_kerja', 'pns_golongan',
        ];

        foreach ($emptyToNull as $col) {
            if (($data[$col] ?? '') === '') {
                $data[$col] = null;
            }
        }

        $data['pns'] = (bool) ($data['pns'] ?? false);
        $data['jumlah_pu'] = isset($data['jumlah_pu']) && $data['jumlah_pu'] !== ''
            ? (int) $data['jumlah_pu'] : null;

        return $data;
    }

    /**
     * @return array{data: array<string, mixed>|null, error: string|null}
     */
    public function buildImportRow(array $row, array $headerIndexes, array $requiredColumns): array
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

    /**
     * @return array{0: array<string, int>, 1: int}
     */
    private function readHeader(string $filePath, string $delimiter): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Tidak dapat membaca file import.');
        }

        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        fclose($handle);

        if ($rawHeaders === false) {
            throw new \RuntimeException('File import kosong.');
        }

        $rawHeaders = $this->expandMergedCsvRow($rawHeaders, $delimiter);

        return [$this->buildHeaderIndexes($rawHeaders), count($rawHeaders)];
    }

    private function countDataRows(string $filePath, string $delimiter): int
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return 0;
        }

        fgetcsv($handle, 0, $delimiter);
        $count = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $count++;
        }
        fclose($handle);

        return $count;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readDataRows(string $filePath, string $delimiter, int $offset, int $limit): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [];
        }

        fgetcsv($handle, 0, $delimiter);

        $skipped = 0;
        while ($skipped < $offset && ($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $skipped++;
        }

        $rows = [];
        $read = 0;
        while ($read < $limit && ($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $this->expandMergedCsvRow($row, $delimiter);
            $read++;
        }

        fclose($handle);

        return $rows;
    }

    private function detectDelimiterFromFile(string $filePath): string
    {
        $firstLine = @file_get_contents($filePath, false, null, 0, 8192);
        if ($firstLine === false) {
            return ',';
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        return $this->detectDelimiter(strtok($firstLine, "\r\n"));
    }

    /**
     * @param  array<int, string>|null  $headers
     * @return array<string, int>
     */
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
            'idpendamping' => 'id_pendamping',
            'kodepos' => 'kode_pos',
            'nohp' => 'no_hp',
            'tglberlaku' => 'tgl_berlaku',
            'tgllahir' => 'tgl_lahir',
            'asalunitkerja' => 'asal_unit_kerja',
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

    /**
     * @param  array<int, string>  $row
     * @return array<int, string>
     */
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

    private function convertToBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === null || $value === '') {
            return false;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y', 'checked', 'on'], true);
    }

    private function convertToDate(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $baseDate = new \DateTime('1899-12-30');
                $baseDate->modify('+'.(int) $value.' days');

                return $baseDate->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
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

        return $message;
    }

    private function convertExcelToCsv(string $excelPath, string $csvPath): void
    {
        $rows = $this->readExcelFile($excelPath);
        if ($rows === []) {
            throw new \InvalidArgumentException('File Excel kosong.');
        }

        $handle = fopen($csvPath, 'w');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readExcelFile(string $filePath): array
    {
        $rows = [];
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $strings = [];
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $xmlString = $zip->getFromName('xl/sharedStrings.xml');
                    $xml = simplexml_load_string($xmlString);
                    foreach ($xml->si as $si) {
                        $strings[] = (string) $si->t;
                    }
                }

                $worksheetFile = 'xl/worksheets/sheet1.xml';
                if ($zip->locateName($worksheetFile) !== false) {
                    $xmlSheet = $zip->getFromName($worksheetFile);
                    $xml = simplexml_load_string($xmlSheet);

                    foreach ($xml->sheetData->row as $row) {
                        $rowData = [];
                        foreach ($row->c as $cell) {
                            $value = '';
                            if (isset($cell->v)) {
                                $cellType = (string) $cell['t'];
                                $value = $cellType === 's'
                                    ? ($strings[(int) $cell->v] ?? '')
                                    : (string) $cell->v;
                            }
                            $rowData[] = $value;
                        }
                        if (! empty(array_filter($rowData))) {
                            $rows[] = $rowData;
                        }
                    }
                }
                $zip->close();
            }
        } catch (\Exception) {
            return [];
        }

        return $rows;
    }
}
