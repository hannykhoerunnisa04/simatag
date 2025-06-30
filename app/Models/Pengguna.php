<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Tambahkan jika ingin fitur verifikasi email Breeze
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str; // Untuk UUID jika Id_pengguna adalah UUID

class Pengguna extends Authenticatable // Implement MustVerifyEmail jika pakai verifikasi email
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pengguna';

    /**
     * Primary key yang terkait dengan tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_pengguna';

    /**
     * Menunjukkan apakah ID model auto-incrementing.
     * Karena Id_pengguna adalah varchar(40), set ke false.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data dari primary key.
     * Karena Id_pengguna adalah varchar (string).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna', // Masukkan jika kamu akan mengisinya secara manual saat create, atau jika di-generate otomatis (lihat boot method)
        'nama',
        'email',
        'password',
        'role',
        'email_verified_at', // Diperlukan oleh Breeze untuk verifikasi email
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // Diperlukan oleh Breeze untuk fitur "Remember Me"
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Otomatis hash password saat diset
    ];

    /**
     * Boot method untuk model.
     * Berguna jika Id_pengguna adalah UUID yang di-generate otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid(); // Membuat UUID otomatis
            }
        });
    }
}