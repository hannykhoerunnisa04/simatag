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
        Schema::table('pengguna', function (Blueprint $table) {
            $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at (keduanya nullable by default jika ditambahkan ke tabel yang sudah ada)
                                 // atau $table->timestamp('created_at')->nullable(); $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropTimestamps(); // Ini akan menghapus created_at dan updated_at
                                    // atau $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};