<?php

namespace Tests\Feature;

use App\Models\Pendamping;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendampingImportTest extends TestCase
{
    public function test_pendamping_data_endpoint_returns_json_list(): void
    {
        Pendamping::create([
            'no_registrasi' => 'LIST-TEST-001',
            'id_pendamping' => 'ID-LIST-001',
            'nama' => 'Test Pendamping',
            'pns' => false,
        ]);

        $response = $this->getJson(route('pendamping.data'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['no_registrasi' => 'LIST-TEST-001', 'nama' => 'Test Pendamping']);
    }

    public function test_semicolon_delimited_csv_imports(): void
    {
        $csv = str_replace(',', ';', file_get_contents(base_path('sample_pendamping.csv')));
        $file = UploadedFile::fake()->createWithContent('pendamping.csv', $csv, 'text/plain');

        $response = $this->postJson(route('pendamping.import'), [
            'file' => $file,
        ]);

        $response->assertOk()
            ->assertJsonPath('imported', 3)
            ->assertJsonPath('skipped', 0);
    }

    public function test_sample_csv_imports_all_rows(): void
    {
        $csv = file_get_contents(base_path('sample_pendamping.csv'));
        $file = UploadedFile::fake()->createWithContent('sample_pendamping.csv', $csv, 'text/plain');

        $response = $this->postJson(route('pendamping.import'), [
            'file' => $file,
        ]);

        $response->assertOk()
            ->assertJsonPath('imported', 3)
            ->assertJsonPath('skipped', 0);
    }

    public function test_csv_import_returns_json(): void
    {
        Storage::fake('local');

        $csv = <<<'CSV'
no_registrasi,id_pendamping,id_lembaga,no_pendaftaran,tgl_berlaku,nama,alamat,kode_pos,kecamatan,kabupaten,provinsi,no_hp,tempat_lahir,tgl_lahir,nik,pendidikan,universitas,status,nama_lembaga,sumber_data,jumlah_pu,pekerjaan,pekerjaan_lain,asal_unit_kerja,pns,pns_golongan
REG-TEST-001,ID-TEST-001,LMB-001,NP-001,01/01/2025,Test Import,Jl Test,12345,Kecamatan Test,Kabupaten Test,DIY,081234567890,Jakarta,01/01/1990,1234567890123456,S1,Universitas Test,Aktif,LP3H UIN,Manual,1,Guru,,Unit Kerja,0,
CSV;
        $file = UploadedFile::fake()->createWithContent('test.csv', $csv, 'text/plain');

        $response = $this->postJson(route('pendamping.import'), [
            'file' => $file,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
    }
}
