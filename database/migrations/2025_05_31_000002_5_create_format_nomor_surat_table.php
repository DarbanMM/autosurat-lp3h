<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('format_nomor_surat', function (Blueprint $table) {
            $table->id('id_format_nomor');
            $table->text('setting_surat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('format_nomor_surat');
    }
};
