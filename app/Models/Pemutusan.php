<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemutusan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pemutusan';

    /**
     * Primary key untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'id_pemutusan';

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
     * Diatur ke false karena tabel Anda tidak memiliki kolom created_at dan updated_at.
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
        'id_pemutusan',
        'id_pelanggan',
        'tgl_pemutusan',
        'alasan_pemutusan',
        'status_pemutusan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data asli.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_pemutusan' => 'date',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Pelanggan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pelanggan()
    {
        // Parameter kedua: foreign key di tabel 'pemutusan'
        // Parameter ketiga: primary key di tabel 'pelanggan'
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
