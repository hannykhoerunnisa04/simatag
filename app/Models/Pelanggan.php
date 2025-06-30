<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Diperbaiki: Menonaktifkan manajemen timestamps otomatis.
     * Baris ini sangat penting untuk ditambahkan.
     *
     * @var bool
     */
    public $timestamps = false; 

    protected $fillable = [
        'id_pelanggan',
        'id_pengguna',
        'nama_pelanggan',
        'alamat',
        'no_hp',
        'status_pelanggan',
        'id_paket',
    ];

    /**
     * Relasi ke model PaketLayanan.
     */
    public function paket()
    {
        return $this->belongsTo(PaketLayanan::class, 'id_paket', 'id_paket');
    }

    /**
     * Relasi ke model Pengguna.
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
