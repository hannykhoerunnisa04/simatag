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
        Schema::table('pembayaran', function (Blueprint $table) {
            // Menambahkan kolom 'file_bukti' setelah kolom 'metode_bayar'
            // Dibuat nullable() agar data lama tidak error
            $table->string('file_bukti')->nullable()->after('metode_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Menghapus kolom 'file_bukti' jika migrasi di-rollback
            $table->dropColumn('file_bukti');
        });
    }
};
