<?php

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
        Schema::table('paket_layanan', function (Blueprint $table) {
            // Menambahkan kolom 'deskripsi' setelah kolom 'harga'
            $table->text('deskripsi')->nullable()->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_layanan', function (Blueprint $table) {
            // Menghapus kolom 'deskripsi' jika migrasi di-rollback
            $table->dropColumn('deskripsi');
        });
    }
};