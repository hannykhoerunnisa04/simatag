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
        // Mengubah kolom menjadi ENUM dengan pilihan yang benar
        $table->enum('metode_bayar', ['Transfer Bank', 'Dompet Digital', 'Lainnya'])->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            //
        });
    }
};
