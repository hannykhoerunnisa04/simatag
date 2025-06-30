<<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->rememberToken()->nullable()->after('password'); // Untuk fitur "Remember Me"
            $table->timestamp('email_verified_at')->nullable()->after('remember_token'); // Untuk verifikasi email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropRememberToken();
            $table->dropColumn('email_verified_at');
        });
    }
};