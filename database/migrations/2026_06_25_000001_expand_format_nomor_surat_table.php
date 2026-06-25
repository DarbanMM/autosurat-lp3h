<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('format_nomor_surat', function (Blueprint $table) {
            $table->string('reset_period')->nullable()->after('setting_surat');
            $table->string('display_format')->nullable()->after('reset_period');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('format_nomor_surat', function (Blueprint $table) {
            $table->dropColumn(['reset_period', 'display_format', 'created_at', 'updated_at']);
        });
    }
};
