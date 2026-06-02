<?php

namespace App\Http\Controllers;

use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendampingController extends Controller
{
    public function import(Request $request)
    {
        // Set timeout for large imports
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:50240'
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

            $headers = array_shift($rows);
            $requiredColumns = [
                'no_registrasi', 'id_pendamping', 'id_lembaga', 'no_pendaftaran',
                'tgl_berlaku', 'nama', 'alamat', 'kode_pos', 'kecamatan', 'kabupaten',
                'provinsi', 'no_hp', 'tempat_lahir', 'tgl_lahir', 'nik', 'pendidikan',
                'universitas', 'status', 'nama_lembaga', 'sumber_data', 'jumlah_pu',
                'pekerjaan', 'pekerjaan_lain', 'asal_unit_kerja', 'pns', 'pns_golongan'
            ];

            $headerMap = array_flip($headers);
            foreach ($requiredColumns as $col) {
                if (!isset($headerMap[$col])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Kolom '{$col}' tidak ditemukan. Pastikan header sudah sesuai."
                    ], 400);
                }
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $rowIndex => $row) {
                if (empty($row[0])) continue;

                try {
                    $data = [];
                    foreach ($requiredColumns as $col) {
                        $value = $row[$headerMap[$col]] ?? null;
                        $value = trim((string)$value);

                        // Skip PNS field - it will be determined by pns_golongan
                        if ($col === 'pns') {
                            continue;
                        }

                        // Handle scientific notation for NIK and phone numbers
                        if (in_array($col, ['nik', 'no_hp']) && preg_match('/^[0-9.E+\-]+$/', $value)) {
                            // Convert scientific notation to regular number
                            $value = sprintf('%.0f', (float)$value);
                        }

                        if (in_array($col, ['tgl_berlaku', 'tgl_lahir'])) {
                            $value = $this->convertToDate($value);
                        } elseif ($col === 'jumlah_pu') {
                            $value = is_numeric($value) ? (int)$value : null;
                        }

                        $data[$col] = $value;
                    }

                    // no_registrasi is required
                    if (empty($data['no_registrasi'])) {
                        $skipped++;
                        continue;
                    }

                    // Set PNS based on whether pns_golongan has a value
                    $data['pns'] = !empty(trim((string)($data['pns_golongan'] ?? '')));

                    Pendamping::updateOrCreate(
                        ['no_registrasi' => $data['no_registrasi']],
                        $data
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil import {$imported} data pendamping.";
            if ($skipped > 0) {
                $message .= " {$skipped} baris dilewatkan.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => array_slice($errors, 0, 5)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function readCsvFile($filePath)
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
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
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                $baseDate = new \DateTime('1899-12-30');
                $baseDate->modify('+' . (int)$value . ' days');
                return $baseDate->format('Y-m-d');
            }

            $date = \DateTime::createFromFormat('d/m/Y', $value)
                ?: \DateTime::createFromFormat('Y-m-d', $value)
                ?: \DateTime::createFromFormat('m/d/Y', $value);

            return $date ? $date->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null;
        }
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

