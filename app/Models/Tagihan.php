<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tagihan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'tagihan';

    /**
     * Primary key untuk tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_tagihan';

    /**
     * Primary key bukan auto-increment karena menggunakan custom ID atau UUID.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Menonaktifkan timestamps otomatis.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_tagihan',
        'id_pelanggan',
        'periode',
        'tgl_jatuh_tempo',
        'jumlah_tagihan',
        'status_tagihan',
    ];

    /**
     * Cast atribut ke tipe data tertentu.
     */
    protected $casts = [
        'tgl_jatuh_tempo' => 'date',
        'jumlah_tagihan' => 'integer',
    ];

    /**
     * Boot method (aktifkan jika ID harus otomatis dibuat).
     * Bisa custom prefix 'TGH-' + random string.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = 'TGH-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Relasi ke model Pelanggan.
     * Setiap tagihan dimiliki oleh satu pelanggan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Relasi ke model Pembayaran.
     * Setiap tagihan mungkin punya satu pembayaran.
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_tagihan', 'id_tagihan');
    }
}
