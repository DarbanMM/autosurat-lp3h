<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_ketua', function (Blueprint $table) {
            $table->string('nip')->primary();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->text('barcode_ttd')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_ketua');
    }
};
