<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('pic')->nullable()->after('status_pelanggan'); // Tambah kolom PIC
            $table->string('email_pic')->nullable()->after('pic');       // Tambah kolom Email PIC
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['pic', 'email_pic']);
        });
    }
};
