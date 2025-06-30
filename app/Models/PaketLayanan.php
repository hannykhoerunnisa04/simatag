<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketLayanan extends Model
{
    use HasFactory;

    protected $table = 'paket_layanan'; // Sesuaikan jika nama tabel Anda berbeda
    protected $primaryKey = 'id_paket';   // Sesuaikan jika primary key Anda berbeda
    public $incrementing = false;         // Jika Id_paket bukan auto-increment
    protected $keyType = 'string';         // Jika Id_paket adalah string

    // Definisikan $fillable jika Anda akan membuat/update record melalui Eloquent
    protected $fillable = [
        'id_paket',
        'nama_paket',
        'kecepatan',
        'harga',
        'deskripsi',
    ];

    // Jika tidak ada kolom created_at dan updated_at
    public $timestamps = false;

    /**
     * Relasi ke Pelanggan (satu paket layanan bisa dimiliki banyak pelanggan)
     */
    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'id_paket', 'id_paket');
    }
}