<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserImport;
use App\Services\UserImportService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(
        private UserImportService $importService
    ) {}

    /**
     * List all users.
     */
    public function index()
    {
        $users = User::orderBy('id')->get()->map(fn (User $user) => [
            'id' => $user->id,
            'username' => $user->username,
            'has_password' => $user->password !== null && $user->password !== '',
            'role' => $user->role,
            'no_registrasi' => $user->no_registrasi,
        ]);

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'nullable|string|min:4',
            'role' => 'required|in:admin,user',
        ]);

        try {
            $user = User::create([
                'username' => $request->input('username'),
                'password' => $request->input('password') ? $request->input('password') : null,
                'role' => $request->input('role'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'has_password' => $user->password !== null,
                    'role' => $user->role,
                    'no_registrasi' => $user->no_registrasi,
                ],
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$id,
            'password' => 'nullable|string|min:4',
            'role' => 'required|in:admin,user',
        ]);

        $data = [
            'username' => $request->input('username'),
            'role' => $request->input('role'),
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        try {
            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync users from pendamping table.
     * Creates user accounts for no_registrasi values that don't have a user yet.
     */
    public function sync()
    {
        // Get all no_registrasi from pendamping
        $pendampingNoReg = DB::table('pendamping')
            ->whereNotNull('no_registrasi')
            ->where('no_registrasi', '!=', '')
            ->pluck('no_registrasi')
            ->toArray();

        if (empty($pendampingNoReg)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada data pendamping untuk disinkronkan.',
                'added' => 0,
                'skipped' => 0,
            ]);
        }

        // Get existing no_registrasi in users table
        $existingNoReg = DB::table('users')
            ->whereNotNull('no_registrasi')
            ->whereIn('no_registrasi', $pendampingNoReg)
            ->pluck('no_registrasi')
            ->flip()
            ->all();

        // Filter to only new no_registrasi
        $newNoReg = array_filter($pendampingNoReg, fn ($nr) => ! isset($existingNoReg[$nr]));

        if (empty($newNoReg)) {
            return response()->json([
                'success' => true,
                'message' => 'Semua data pendamping sudah tersinkron. Tidak ada user baru yang ditambahkan.',
                'added' => 0,
                'skipped' => count($pendampingNoReg),
            ]);
        }

        // Also check against existing usernames to avoid unique constraint violations
        $existingUsernames = DB::table('users')
            ->whereIn('username', $newNoReg)
            ->pluck('username')
            ->flip()
            ->all();

        $now = now()->toDateTimeString();
        $toInsert = [];
        $skippedUsername = 0;

        foreach ($newNoReg as $noReg) {
            if (isset($existingUsernames[$noReg])) {
                $skippedUsername++;
                continue;
            }

            $toInsert[] = [
                'username' => $noReg,
                'password' => null,
                'role' => 'user',
                'no_registrasi' => $noReg,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $added = 0;
        if (! empty($toInsert)) {
            // Insert in chunks to avoid memory issues
            foreach (array_chunk($toInsert, 500) as $chunk) {
                DB::table('users')->insert($chunk);
                $added += count($chunk);
            }
        }

        $totalSkipped = count($existingNoReg) + $skippedUsername;

        return response()->json([
            'success' => true,
            'message' => "Sinkronisasi selesai. {$added} user baru ditambahkan."
                .($totalSkipped > 0 ? " {$totalSkipped} data sudah ada (dilewatkan)." : ''),
            'added' => $added,
            'skipped' => $totalSkipped,
        ]);
    }

    // ─── Import (Chunked) ────────────────────────────────────────────────

    /**
     * Prepare import: upload file, detect headers, return import ID.
     */
    public function prepareImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:10240',
        ]);

        try {
            $import = $this->importService->prepareFromUpload($request->file('file'));

            return response()->json([
                'success' => true,
                'import_id' => $import->id,
                'total_rows' => $import->total_rows,
                'chunk_size' => $this->importService->chunkSize(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process one chunk of an import.
     */
    public function importChunk(Request $request, string $importId)
    {
        $import = UserImport::findOrFail($importId);
        $offset = (int) $request->input('offset', 0);

        try {
            $result = $this->importService->processChunk($import, $offset);

            return response()->json([
                'success' => true,
                'message' => $result['finished']
                    ? "Berhasil import {$result['imported_count']} user."
                        .($result['skipped_count'] > 0 ? " {$result['skipped_count']} baris dilewatkan." : '')
                    : null,
                ...$result,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: '.$e->getMessage()], 500);
        }
    }

    /**
     * Get import status.
     */
    public function importStatus(string $importId)
    {
        $import = UserImport::findOrFail($importId);

        return response()->json($this->importService->statusPayload($import));
    }

    // ─── Export ──────────────────────────────────────────────────────────

    /**
     * Export users as CSV.
     */
    public function export(string $format = 'csv')
    {
        $users = User::orderBy('id')->get();

        if ($format === 'xlsx') {
            return $this->exportXlsx($users);
        }

        $filename = 'export_users_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['username', 'role', 'no_registrasi', 'password_status']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->username,
                    $user->role,
                    $user->no_registrasi ?? '',
                    $user->password ? 'Sudah di-assign' : 'Belum di-assign',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function exportXlsx($users): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'export_users_'.date('Y-m-d').'.xlsx';

        return response()->stream(function () use ($users) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
            $zip = new \ZipArchive();
            $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $zip->addFromString('[Content_Types].xml',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
                .'</Types>'
            );

            $zip->addFromString('_rels/.rels',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>'
            );

            $zip->addFromString('xl/_rels/workbook.xml.rels',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
                .'</Relationships>'
            );

            $zip->addFromString('xl/workbook.xml',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>'
            );

            $columns = ['username', 'role', 'no_registrasi', 'password_status'];
            $allStrings = $columns;
            foreach ($users as $user) {
                $allStrings[] = $user->username;
                $allStrings[] = $user->role;
                $allStrings[] = $user->no_registrasi ?? '';
                $allStrings[] = $user->password ? 'Sudah di-assign' : 'Belum di-assign';
            }

            $uniqueStrings = array_values(array_unique($allStrings));
            $stringMap = array_flip($uniqueStrings);

            $strings = '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($allStrings).'" uniqueCount="'.count($uniqueStrings).'">';
            foreach ($uniqueStrings as $str) {
                $strings .= '<si><t>'.htmlspecialchars($str).'</t></si>';
            }
            $strings .= '</sst>';
            $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.$strings);

            $colLetters = ['A', 'B', 'C', 'D'];
            $sheet = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
            
            // Header row
            $sheet .= '<row r="1">';
            foreach ($columns as $i => $col) {
                $sheet .= '<c r="'.$colLetters[$i].'1" t="s"><v>'.$stringMap[$col].'</v></c>';
            }
            $sheet .= '</row>';

            // Data rows
            $rowIndex = 2;
            foreach ($users as $user) {
                $sheet .= '<row r="'.$rowIndex.'">';
                $rowData = [
                    $user->username,
                    $user->role,
                    $user->no_registrasi ?? '',
                    $user->password ? 'Sudah di-assign' : 'Belum di-assign'
                ];
                foreach ($rowData as $i => $val) {
                    $sheet .= '<c r="'.$colLetters[$i].$rowIndex.'" t="s"><v>'.$stringMap[$val].'</v></c>';
                }
                $sheet .= '</row>';
                $rowIndex++;
            }
            
            $sheet .= '</sheetData></worksheet>';
            $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.$sheet);

            $zip->close();

            readfile($tmpFile);
            @unlink($tmpFile);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Template Download ──────────────────────────────────────────────

    /**
     * Download import template (CSV or Excel).
     */
    public function downloadTemplate(string $format = 'csv')
    {
        $columns = ['username', 'password', 'role'];

        if ($format === 'xlsx') {
            return $this->downloadXlsxTemplate($columns);
        }

        $filename = 'template_user.csv';

        return response()->stream(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fputcsv($handle, array_fill(0, count($columns), ''));
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function downloadXlsxTemplate(array $columns): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'template_user.xlsx';

        return response()->stream(function () use ($columns) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
            $zip = new \ZipArchive();
            $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $zip->addFromString('[Content_Types].xml',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
                .'</Types>'
            );

            $zip->addFromString('_rels/.rels',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>'
            );

            $zip->addFromString('xl/_rels/workbook.xml.rels',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
                .'</Relationships>'
            );

            $zip->addFromString('xl/workbook.xml',
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>'
            );

            $strings = '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($columns).'" uniqueCount="'.count($columns).'">';
            foreach ($columns as $col) {
                $strings .= '<si><t>'.htmlspecialchars($col).'</t></si>';
            }
            $strings .= '</sst>';
            $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.$strings);

            $colLetters = range('A', 'Z');
            $sheet = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1">';
            foreach ($columns as $i => $col) {
                $sheet .= '<c r="'.$colLetters[$i].'1" t="s"><v>'.$i.'</v></c>';
            }
            $sheet .= '</row></sheetData></worksheet>';
            $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.$sheet);

            $zip->close();

            readfile($tmpFile);
            @unlink($tmpFile);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
