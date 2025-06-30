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
    Schema::table('pelanggan', function (Blueprint $table) {
        // Mengubah kolom menjadi ENUM dengan pilihan yang benar
        $table->enum('status_pelanggan', ['aktif', 'tidak aktif', 'putus'])->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            //
        });
    }
};
