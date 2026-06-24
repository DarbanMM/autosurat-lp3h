<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserImport;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserImportService
{
    public const REQUIRED_COLUMNS = ['username', 'password', 'role'];

    private const CHUNK_SIZE = 100;

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    public function prepareFromUpload(UploadedFile $file): UserImport
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['csv', 'xlsx', 'xls'], true)) {
            throw new \InvalidArgumentException('Format file harus CSV atau Excel.');
        }

        $importId = (string) Str::uuid();
        $storedPath = 'user_imports/'.$importId.'.'.$extension;
        Storage::disk('local')->putFileAs('user_imports', $file, $importId.'.'.$extension);
        $absolutePath = Storage::disk('local')->path($storedPath);

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $csvPath = Storage::disk('local')->path('user_imports/'.$importId.'.csv');
            $this->convertExcelToCsv($absolutePath, $csvPath);
            Storage::disk('local')->delete($storedPath);
            $storedPath = 'user_imports/'.$importId.'.csv';
            $absolutePath = $csvPath;
        }

        $delimiter = $this->detectDelimiter($absolutePath);
        [$headerMap, $columnCount] = $this->readHeader($absolutePath, $delimiter);
        $missing = array_values(array_diff(self::REQUIRED_COLUMNS, array_keys($headerMap)));
        if ($missing !== []) {
            Storage::disk('local')->delete($storedPath);
            throw new \InvalidArgumentException(
                'Kolom tidak ditemukan: '.implode(', ', $missing).'. Gunakan template CSV/Excel.'
            );
        }

        $totalRows = $this->countDataRows($absolutePath, $delimiter);

        return UserImport::create([
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

    public function processChunk(UserImport $import, int $offset, ?int $limit = null): array
    {
        $limit = $limit ?? self::CHUNK_SIZE;
        $import->update([
            'status' => 'processing',
            'started_at' => $import->started_at ?? now(),
        ]);

        $path = Storage::disk('local')->path($import->file_path);
        $rows = $this->readDataRows($path, $import->delimiter, $offset, $limit);
        $result = $this->processRows($import, $rows, $offset + 2);

        $import->refresh();
        $newProcessed = min($import->total_rows, $offset + count($rows));

        $import->update([
            'processed_rows' => $newProcessed,
            'imported_count' => $import->imported_count + $result['imported'],
            'skipped_count' => $import->skipped_count + $result['skipped'],
            'errors' => array_slice(
                array_merge($import->errors ?? [], $result['errors']),
                -50
            ),
        ]);

        $import->refresh();
        $finished = $import->processed_rows >= $import->total_rows;

        if ($finished) {
            $import->update([
                'status' => 'completed',
                'completed_at' => now(),
                'message' => "Berhasil import {$import->imported_count} user."
                    .($import->skipped_count > 0 ? " {$import->skipped_count} baris dilewatkan." : ''),
            ]);
        }

        return [
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'errors' => array_slice($import->errors ?? [], 0, 10),
            'finished' => $finished,
            'processed_rows' => $import->processed_rows,
            'total_rows' => $import->total_rows,
            'imported_count' => $import->imported_count,
            'skipped_count' => $import->skipped_count,
            'progress_percent' => $import->progressPercent(),
        ];
    }

    public function statusPayload(UserImport $import): array
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
    private function processRows(UserImport $import, array $rows, int $startLineNumber): array
    {
        $headerMap = $import->header_map ?? [];
        $columnCount = $import->column_count;
        $imported = 0;
        $skipped = 0;
        $errors = [];

        // Pre-fetch existing usernames for duplicate check
        $usernames = [];
        foreach ($rows as $row) {
            $row = array_pad($row, $columnCount, '');
            $username = trim((string) ($row[$headerMap['username'] ?? 0] ?? ''));
            if ($username !== '') {
                $usernames[] = $username;
            }
        }

        $existingUsernames = [];
        if (! empty($usernames)) {
            $existingUsernames = DB::table('users')
                ->whereIn('username', $usernames)
                ->pluck('username')
                ->flip()
                ->all();
        }

        foreach ($rows as $index => $row) {
            $row = array_pad($row, $columnCount, '');
            $lineNumber = $startLineNumber + $index;

            if ($this->isRowEmpty($row)) {
                continue;
            }

            $username = trim((string) ($row[$headerMap['username'] ?? 0] ?? ''));
            $password = trim((string) ($row[$headerMap['password'] ?? 1] ?? ''));
            $role = strtolower(trim((string) ($row[$headerMap['role'] ?? 2] ?? 'user')));

            if ($username === '') {
                $skipped++;
                $errors[] = "Baris {$lineNumber}: Username kosong";
                continue;
            }

            if (isset($existingUsernames[$username])) {
                $skipped++;
                $errors[] = "Baris {$lineNumber}: Data sudah ada (username: {$username})";
                continue;
            }

            if (! in_array($role, ['admin', 'user'], true)) {
                $role = 'user';
            }

            try {
                User::create([
                    'username' => $username,
                    'password' => $password !== '' ? $password : null,
                    'role' => $role,
                ]);
                $existingUsernames[$username] = true;
                $imported++;
            } catch (QueryException $e) {
                $skipped++;
                if (str_contains($e->getMessage(), 'users_username_unique')) {
                    $errors[] = "Baris {$lineNumber}: Data sudah ada (username: {$username})";
                } else {
                    $errors[] = "Baris {$lineNumber}: ".$e->getMessage();
                }
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    // ─── File parsing helpers ───────────────────────────────────────────

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

        $indexes = [];
        foreach ($rawHeaders as $index => $header) {
            $canonical = $this->canonicalHeaderName((string) $header);
            if ($canonical !== '' && ! isset($indexes[$canonical])) {
                $indexes[$canonical] = $index;
            }
        }

        return [$indexes, count($rawHeaders)];
    }

    private function countDataRows(string $filePath, string $delimiter): int
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return 0;
        }

        fgetcsv($handle, 0, $delimiter);
        $count = 0;
        while (fgetcsv($handle, 0, $delimiter) !== false) {
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

        fgetcsv($handle, 0, $delimiter); // skip header

        $skipped = 0;
        while ($skipped < $offset && fgetcsv($handle, 0, $delimiter) !== false) {
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

    private function detectDelimiter(string $filePath): string
    {
        $firstLine = @file_get_contents($filePath, false, null, 0, 8192);
        if ($firstLine === false) {
            return ',';
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $line = strtok($firstLine, "\r\n");

        $counts = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];
        arsort($counts);
        $delimiter = array_key_first($counts);

        return ($counts[$delimiter] ?? 0) > 0 ? $delimiter : ',';
    }

    private function canonicalHeaderName(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
        $header = strtolower(trim($header));
        $header = preg_replace('/[\s\-]+/', '_', $header);

        return preg_replace('/[^a-z0-9_]/', '', $header);
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

    // ─── Excel helpers ──────────────────────────────────────────────────

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
