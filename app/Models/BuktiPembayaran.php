<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk UUID jika id_bukti adalah UUID

class BuktiPembayaran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bukti_pembayaran';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_bukti';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_bukti',
        'id_pembayaran',
        'file_bukti',   // Jika Anda menyimpan path file sebagai string.
                        // Jika BLOB dan dihandle berbeda, sesuaikan.
        'catatan_adm',
        'status',       // enum(‘valid’, ‘tidak valid’, ‘menunggu’)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'status' => 'string', // Enum biasanya diperlakukan sebagai string
        'created_at' => 'datetime', // Jika Anda menggunakan timestamps
        'updated_at' => 'datetime', // Jika Anda menggunakan timestamps
    ];

    /**
     * Boot method for the model.
     * Useful if id_bukti is a UUID that is auto-generated.
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
     * Get the pembayaran that owns the bukti pembayaran.
     * Satu bukti pembayaran dimiliki oleh satu pembayaran.
     */
    public function pembayaran()
    {
        // Parameter kedua adalah foreign key di tabel 'bukti_pembayaran' (id_pembayaran)
        // Parameter ketiga adalah primary key di tabel 'pembayaran' (Id_pembayaran)
        return $this->belongsTo(Pembayaran::class, 'id_pembayaran', 'Id_pembayaran');
    }

    // Jika tabel 'bukti_pembayaran' Anda TIDAK memiliki kolom 'created_at' dan 'updated_at',
    // maka hapus tanda komentar (//) dari baris di bawah ini untuk menonaktifkan timestamps.
    // public $timestamps = false;
}
