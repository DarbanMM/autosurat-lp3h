<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportUserCsvTest extends TestCase
{
    public function test_user_csv_file_imports_via_chunks(): void
    {
        $path = base_path('database/Fewer CSV Data Pendamping.csv');
        if (! file_exists($path)) {
            $this->markTestSkipped('User CSV file not present.');
        }

        $file = new UploadedFile($path, 'Fewer CSV Data Pendamping.csv', 'text/csv', null, true);

        $prepare = $this->postJson(route('pendamping.import.prepare'), ['file' => $file]);
        $prepare->assertOk();

        $importId = $prepare->json('import_id');
        $total = $prepare->json('total_rows');
        $chunkSize = $prepare->json('chunk_size');
        $offset = 0;

        while ($offset < $total) {
            $chunk = $this->postJson(route('pendamping.import.chunk', $importId), ['offset' => $offset]);
            $chunk->assertOk();
            $offset += $chunkSize;
            if ($chunk->json('finished')) {
                $this->assertGreaterThan(40, $chunk->json('imported_count'));
                break;
            }
        }
    }
}
