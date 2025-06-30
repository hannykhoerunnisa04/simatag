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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->string('Id_pengguna', 40)->primary(); // varchar(40), Primary Key
            $table->string('nama', 100);                 // Varchar(100)
            $table->string('email', 100);                // Varchar(100)
            $table->string('password', 255);             // Varchar(255)
            $table->enum('role', ['admin', 'pelanggan', 'atasan']); // Enum(‘admin’,’pelanggan’, ‘atasan’)
            // $table->timestamps(); // Opsional, jika kamu butuh kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};