<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendamping', function (Blueprint $table) {
            $table->string('no_registrasi')->primary();
            $table->string('id_pendamping')->unique();
            $table->string('id_lembaga')->nullable();
            $table->string('no_pendaftaran')->nullable();
            $table->date('tgl_berlaku')->nullable();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nik')->unique()->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('universitas')->nullable();
            $table->string('status')->nullable();
            $table->string('nama_lembaga')->nullable();
            $table->string('sumber_data')->nullable();
            $table->integer('jumlah_pu')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('pekerjaan_lain')->nullable();
            $table->string('asal_unit_kerja')->nullable();
            $table->boolean('pns')->default(false);
            $table->string('pns_golongan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendamping');
    }
};
