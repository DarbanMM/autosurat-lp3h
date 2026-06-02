<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = Schema::getColumnListing('users');
            $toDrop = [];

            if (in_array('name', $columns)) $toDrop[] = 'name';
            if (in_array('email', $columns)) $toDrop[] = 'email';
            if (in_array('email_verified_at', $columns)) $toDrop[] = 'email_verified_at';
            if (in_array('remember_token', $columns)) $toDrop[] = 'remember_token';

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('password');
            }
            if (!Schema::hasColumn('users', 'no_registrasi')) {
                $table->string('no_registrasi')->nullable()->unique()->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'no_registrasi']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('password');
        });
    }
};
