<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pelanggan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'pelanggan';

    /**
     * Primary key untuk tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_pelanggan';

    /**
     * Primary key bukan auto-increment karena menggunakan UUID.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data primary key (UUID berarti string).
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
     * Kolom yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
   protected $fillable = [
    'id_pelanggan',
    'id_pengguna',
    'nama_pelanggan',
    'alamat',
    'no_hp',
    'id_paket',
    'status_pelanggan',
    'pic',
    'email_pic',
];


    /**
     * Boot method untuk otomatis membuat UUID jika id_pelanggan kosong.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke model PaketLayanan.
     * Setiap pelanggan terhubung ke satu paket layanan.
     */
    public function paket()
    {
        return $this->belongsTo(PaketLayanan::class, 'id_paket', 'id_paket');
    }

    /**
     * Relasi ke model Pengguna.
     * Setiap pelanggan milik satu pengguna.
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Relasi ke model Tagihan.
     * Setiap pelanggan bisa memiliki banyak tagihan.
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
