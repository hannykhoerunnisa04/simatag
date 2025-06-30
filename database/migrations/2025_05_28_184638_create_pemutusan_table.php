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
        Schema::create('pemutusan', function (Blueprint $table) {
            $table->string('id_pemutusan', 40)->primary(); // Varchar(40), Primary Key
            $table->string('id_pelanggan', 40);           // Varchar(40), Foreign key
            $table->date('tgl_pemutusan');               // date
            $table->text('alasan_pemutusan')->nullable();  // text
            $table->string('status_pemutusan', 100);     // Varchar(100), Contoh: “sementara”, “permanen”, “selesai”
            // Jika Anda lebih suka menggunakan ENUM untuk status_pemutusan:
            // $table->enum('status_pemutusan', ['sementara', 'permanen', 'selesai']);

            // Definisi Foreign Key
            // Pastikan tabel 'pelanggan' sudah ada dan memiliki kolom 'Id_pelanggan'
            $table->foreign('id_pelanggan')->references('Id_pelanggan')->on('pelanggan')->onDelete('cascade')->onUpdate('cascade');

            // $table->timestamps(); // Opsional, jika kamu butuh kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemutusan');
    }
};