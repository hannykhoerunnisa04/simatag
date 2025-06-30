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
        Schema::create('paket_layanan', function (Blueprint $table) {
            $table->string('id_paket', 40)->primary();    // varchar(40), Primary Key
            $table->string('nama_paket', 100);           // Varchar(100)
            $table->string('kecepatan', 40)->nullable(); // Varchar(40), Misal: 50 mbps
            $table->text('harga')->nullable();             // text, untuk harga
            $table->timestamps(); // Opsional, jika kamu butuh kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_layanan');
    }
};