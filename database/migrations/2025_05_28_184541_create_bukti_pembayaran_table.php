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
        Schema::create('bukti_pembayaran', function (Blueprint $table) {
            $table->string('id_bukti', 40)->primary(); // varchar(40), Primary Key
            $table->string('id_pembayaran', 40);       // Varchar(40), Foreign key
            $table->binary('file_bukti');              // BLOB untuk menyimpan file bukti
            $table->text('catatan_adm')->nullable();   // Text, opsional
            $table->enum('status', ['valid', 'tidak valid', 'menunggu']); // Enum

            // Definisi Foreign Key
            // Pastikan tabel 'pembayaran' sudah ada dan memiliki kolom 'Id_pembayaran'
            $table->foreign('id_pembayaran')->references('Id_pembayaran')->on('pembayaran')->onDelete('cascade')->onUpdate('cascade');

            // $table->timestamps(); // Opsional, jika kamu butuh kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_pembayaran');
    }
};