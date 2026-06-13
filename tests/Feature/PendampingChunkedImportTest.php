<?php

namespace Tests\Feature;

use App\Models\PendampingImport;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PendampingChunkedImportTest extends TestCase
{
    public function test_chunked_import_completes_sample_csv(): void
    {
        $csv = file_get_contents(base_path('sample_pendamping.csv'));
        $file = UploadedFile::fake()->createWithContent('sample.csv', $csv, 'text/plain');

        $prepare = $this->postJson(route('pendamping.import.prepare'), ['file' => $file]);
        $prepare->assertOk()->assertJsonPath('success', true);

        $importId = $prepare->json('import_id');
        $total = $prepare->json('total_rows');
        $chunkSize = $prepare->json('chunk_size');

        $offset = 0;
        while ($offset < $total) {
            $chunk = $this->postJson(route('pendamping.import.chunk', $importId), ['offset' => $offset]);
            $chunk->assertOk()->assertJsonPath('success', true);
            $offset += $chunkSize;
            if ($chunk->json('finished')) {
                break;
            }
        }

        $import = PendampingImport::findOrFail($importId);
        $this->assertSame('completed', $import->status);
        $this->assertGreaterThan(0, $import->imported_count);
    }

    public function test_import_status_endpoint(): void
    {
        $import = PendampingImport::create([
            'original_filename' => 'test.csv',
            'file_path' => 'imports/test.csv',
            'total_rows' => 100,
            'processed_rows' => 50,
            'imported_count' => 45,
            'skipped_count' => 5,
            'status' => 'processing',
        ]);

        $response = $this->getJson(route('pendamping.import.status', $import->id));
        $response->assertOk()
            ->assertJsonPath('progress_percent', 50)
            ->assertJsonPath('imported_count', 45);
    }
}
