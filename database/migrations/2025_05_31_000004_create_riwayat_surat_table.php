<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_surat', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->timestamp('tgl_dibuat')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('nama_surat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_surat');
    }
};
