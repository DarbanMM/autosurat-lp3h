<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportUserCsvTest extends TestCase
{
    public function test_user_csv_file_imports_successfully(): void
    {
        $path = base_path('database/Fewer CSV Data Pendamping.csv');
        if (! file_exists($path)) {
            $this->markTestSkipped('User CSV file not present.');
        }

        $file = new UploadedFile($path, 'Fewer CSV Data Pendamping.csv', 'text/csv', null, true);

        $response = $this->postJson(route('pendamping.import'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $this->assertGreaterThan(40, $response->json('imported'), $response->json('message') . ' ' . json_encode($response->json('errors')));
    }
}
