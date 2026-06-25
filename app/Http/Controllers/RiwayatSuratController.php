<?php

namespace App\Http\Controllers;

use App\Models\RiwayatSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RiwayatSuratController extends Controller
{
    /**
     * Get paginated and filtered history records.
     */
    public function index(Request $request)
    {
        $query = $this->buildFilterQuery($request);

        // Sort by latest created_at or tgl_dibuat
        $query->orderBy('created_at', 'desc')->orderBy('id_riwayat', 'desc');

        $perPage = 100;
        $riwayat = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $riwayat->items(),
            'pagination' => [
                'current_page' => $riwayat->currentPage(),
                'last_page' => $riwayat->lastPage(),
                'per_page' => $riwayat->perPage(),
                'total' => $riwayat->total(),
            ],
        ]);
    }

    /**
     * Export history records to Excel (.xlsx), CSV, or PDF.
     */
    public function export(Request $request)
    {
        $query = $this->buildFilterQuery($request);
        $query->orderBy('created_at', 'desc')->orderBy('id_riwayat', 'desc');
        $records = $query->get();

        $format = $request->input('format', 'xlsx');

        if ($format === 'csv') {
            return $this->exportCsv($records);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($records);
        }

        return $this->exportXlsx($records);
    }

    private function exportPdf($records)
    {
        $filename = 'riwayat_surat_'.date('Y-m-d_H-i-s').'.pdf';

        // Format data
        $exportData = [];
        foreach ($records as $index => $record) {
            $keteranganStr = '';
            if (is_array($record->keterangan)) {
                foreach ($record->keterangan as $key => $val) {
                    $keteranganStr .= "{$key}: {$val}<br>";
                }
            } else {
                $keteranganStr = $record->keterangan ?? '';
            }

            $tglDibuat = $record->tgl_dibuat ? $record->tgl_dibuat->format('d M Y H:i:s') : '';

            $exportData[] = [
                'no' => $index + 1,
                'tgl_dibuat' => $tglDibuat,
                'nama_surat' => $record->nama_surat ?? '',
                'nomor_surat' => $record->nomor_surat ?? '',
                'keterangan' => $keteranganStr,
            ];
        }

        // Generate HTML for PDF
        $html = '<h2 style="text-align: center; font-family: sans-serif;">Riwayat Surat Keluar</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 12px;">';
        $html .= '<thead style="background-color: #f3f4f6;"><tr>';
        $html .= '<th style="width: 5%;">No</th>';
        $html .= '<th style="width: 20%;">Tanggal Dibuat</th>';
        $html .= '<th style="width: 25%;">Nama Surat</th>';
        $html .= '<th style="width: 20%;">Nomor Surat</th>';
        $html .= '<th style="width: 30%;">Keterangan</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($exportData as $row) {
            $html .= '<tr>';
            $html .= '<td style="text-align: center;">'.$row['no'].'</td>';
            $html .= '<td>'.$row['tgl_dibuat'].'</td>';
            $html .= '<td>'.$row['nama_surat'].'</td>';
            $html .= '<td>'.$row['nomor_surat'].'</td>';
            $html .= '<td>'.$row['keterangan'].'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }

    private function exportCsv($records)
    {
        $filename = 'riwayat_surat_'.date('Y-m-d_H-i-s').'.csv';
        $columns = ['No', 'Tanggal Dibuat', 'Nama Surat', 'Nomor Surat', 'Keterangan'];

        $callback = function() use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $index => $record) {
                $keteranganStr = '';
                if (is_array($record->keterangan)) {
                    foreach ($record->keterangan as $key => $val) {
                        $keteranganStr .= "{$key}: {$val}\n";
                    }
                } else {
                    $keteranganStr = $record->keterangan ?? '';
                }

                $tglDibuat = $record->tgl_dibuat ? $record->tgl_dibuat->format('d M Y H:i:s') : '';

                fputcsv($file, [
                    $index + 1,
                    $tglDibuat,
                    $record->nama_surat ?? '',
                    $record->nomor_surat ?? '',
                    trim($keteranganStr)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportXlsx($records)
    {
        $filename = 'riwayat_surat_'.date('Y-m-d_H-i-s').'.xlsx';

        return response()->stream(function () use ($records) {
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

            $columns = ['No', 'Tanggal Dibuat', 'Nama Surat', 'Nomor Surat', 'Keterangan'];
            $allStrings = $columns;

            // Prepare dynamic data
            $exportData = [];
            foreach ($records as $index => $record) {
                // Convert keterangan array to string
                $keteranganStr = '';
                if (is_array($record->keterangan)) {
                    foreach ($record->keterangan as $key => $val) {
                        $keteranganStr .= "{$key}: {$val}\n";
                    }
                } else {
                    $keteranganStr = $record->keterangan ?? '';
                }

                $tglDibuat = $record->tgl_dibuat ? $record->tgl_dibuat->format('d M Y H:i:s') : '';

                $rowData = [
                    (string)($index + 1),
                    $tglDibuat,
                    $record->nama_surat ?? '',
                    $record->nomor_surat ?? '',
                    trim($keteranganStr),
                ];
                $exportData[] = $rowData;

                foreach ($rowData as $val) {
                    $allStrings[] = $val;
                }
            }

            $uniqueStrings = array_values(array_unique($allStrings));
            $stringMap = array_flip($uniqueStrings);

            $strings = '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($allStrings).'" uniqueCount="'.count($uniqueStrings).'">';
            foreach ($uniqueStrings as $str) {
                $strings .= '<si><t xml:space="preserve">'.htmlspecialchars($str).'</t></si>';
            }
            $strings .= '</sst>';
            $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.$strings);

            $colLetters = ['A', 'B', 'C', 'D', 'E'];
            $sheet = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
            
            // Header row
            $sheet .= '<row r="1">';
            foreach ($columns as $i => $col) {
                $sheet .= '<c r="'.$colLetters[$i].'1" t="s"><v>'.$stringMap[$col].'</v></c>';
            }
            $sheet .= '</row>';

            // Data rows
            $rowIndex = 2;
            foreach ($exportData as $rowData) {
                $sheet .= '<row r="'.$rowIndex.'">';
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

    /**
     * Build the query based on filter parameters.
     */
    private function buildFilterQuery(Request $request)
    {
        $query = RiwayatSurat::query();

        $tipe = $request->input('tipe');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        if ($tipe === 'minggu') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        } elseif ($tipe === 'bulan') {
            if ($bulan) {
                $query->whereMonth('created_at', $bulan);
            }
            if ($tahun) {
                $query->whereYear('created_at', $tahun);
            }
        } elseif ($tipe === 'tahun') {
            if ($tahun) {
                $query->whereYear('created_at', $tahun);
            }
        }

        return $query;
    }
}
