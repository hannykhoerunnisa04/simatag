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
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->string('id_pelanggan', 40)->primary(); // varchar(40), Primary Key
            $table->string('id_pengguna', 40);           // Varchar(40), Foreign key
            $table->string('nama_pelanggan', 40);           // Varchar(40), Foreign key
            $table->text('alamat')->nullable();          // Text, boleh null jika alamat tidak wajib
            $table->string('no_hp', 20)->nullable();     // Varchar(20), boleh null jika no_hp tidak wajib
            $table->enum('status_pelanggan', ['aktif', 'nonaktif', 'putus']); // Enum
            $table->string('id_paket', 40);              // Varchar(40), Foreign key

            // Definisi Foreign Key
            $table->foreign('Id_pengguna')->references('Id_pengguna')->on('pengguna')->onDelete('cascade')->onUpdate('cascade');
            // Asumsi tabel 'paket_layanan' memiliki kolom 'Id_paket' sebagai primary key atau unique key
            $table->foreign('Id_paket')->references('Id_paket')->on('paket_layanan')->onDelete('restrict')->onUpdate('cascade');
            // $table->timestamps(); // Opsional, jika butuh kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};