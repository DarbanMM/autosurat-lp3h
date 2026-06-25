<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('format_nomor_counter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_format_nomor')->constrained('format_nomor_surat', 'id_format_nomor')->onDelete('cascade');
            $table->string('period_key'); // e.g. "2026-06" (Bulanan), "2026" (Tahunan), "2026-W26" (Mingguan)
            $table->unsignedInteger('last_nomor')->default(0);
            $table->timestamps();

            $table->unique(['id_format_nomor', 'period_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('format_nomor_counter');
    }
};
