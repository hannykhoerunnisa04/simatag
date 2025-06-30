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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->string('Id_tagihan', 40)->primary();         // varchar(40), Primary Key
            $table->string('Id_pelanggan', 40);                  // Varchar(40), Foreign key
            $table->string('periode', 20);                       // Varchar(20), Misal: Juli 2025
            $table->date('tgl_jatuh_tempo')->nullable();         // Date
            $table->decimal('Jumlah_tagihan', 10, 2);            // Decimal(10,2)
            $table->enum('Status_tagihan', ['lunas', 'belum', 'telat']); // Enum

            // Definisi Foreign Key
            // Pastikan tabel 'pelanggan' sudah ada dan memiliki kolom 'Id_pelanggan'
            $table->foreign('Id_pelanggan')->references('Id_pelanggan')->on('pelanggan')->onDelete('cascade')->onUpdate('cascade');

            // $table->timestamps(); // Opsional, jika butuh kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};