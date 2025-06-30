<?php

// Pastikan namespace sesuai dengan lokasi file
namespace App\Models\Pelanggan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pelanggan as ProfilPelanggan; 
use App\Models\Pembayaran; // Import model Pembayaran

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    protected $primaryKey = 'id_tagihan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tagihan',
        'id_pelanggan',
        'periode',
        'tgl_jatuh_tempo',
        'jumlah_tagihan',
        'status_tagihan',
    ];

    protected $casts = [
        'tgl_jatuh_tempo' => 'date',
    ];

    /**
     * Relasi "belongsTo" ke model Pelanggan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(ProfilPelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Ditambahkan: Relasi "hasOne" ke model Pembayaran.
     * Satu tagihan memiliki satu bukti pembayaran.
     */
    public function pembayaran()
    {
        // Parameter kedua: foreign key di tabel 'pembayaran'
        // Parameter ketiga: primary key di tabel ini ('tagihan')
        return $this->hasOne(Pembayaran::class, 'Id_tagihan', 'id_tagihan');
    }
}
