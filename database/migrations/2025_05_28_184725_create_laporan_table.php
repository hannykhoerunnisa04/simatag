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
        Schema::create('laporan', function (Blueprint $table) {
            $table->string('id_laporan', 40)->primary();      // varchar(40), Primary Key
            $table->string('id_admin', 40);                  // Varchar(40), Foreign key
            $table->string('jenis_laporan', 50);             // Varchar(50)
            $table->string('periode_laporan', 20)->nullable(); // Varchar(20), opsional
            $table->binary('file_laporan')->nullable();        // BLOB untuk dokumen file, bisa juga path string

            // Definisi Foreign Key
            // Asumsi 'id_admin' merujuk ke 'Id_pengguna' di tabel 'pengguna'
            // Pastikan tabel 'pengguna' sudah ada dan memiliki kolom 'Id_pengguna'
            $table->foreign('id_admin')->references('Id_pengguna')->on('pengguna')->onDelete('restrict')->onUpdate('cascade');

            $table->timestamps(); // Opsional, tambahkan jika butuh created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};