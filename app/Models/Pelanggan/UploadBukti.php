<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pembayaran';

    /**
     * Primary key untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'id_bukti';

    /**
     * Menunjukkan jika ID model auto-incrementing atau tidak.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data dari primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Menunjukkan jika model harus diberi stempel waktu.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_bukti',
        'id_tagihan',
        'tgl_bayar',
        'metode_bayar',
        'file_bukti',
        'status_validasi',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data asli.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_bayar' => 'datetime',
    ];

    /**
     * Boot method untuk model.
     * Mengisi id_bukti secara otomatis saat membuat record baru.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = 'BKT-' . strtoupper(Str::random(12));
            }
        });
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Tagihan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }
}
