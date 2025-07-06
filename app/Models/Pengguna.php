<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Uncomment kalau butuh email verification
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Pengguna extends Authenticatable // Tambahkan MustVerifyEmail jika pakai email verification
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
     * Menunjukkan apakah primary key auto increment.
     * Karena id_pengguna bertipe string (UUID atau custom ID), maka false.
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
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'nama',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang di-cast ke tipe lain.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel >=10 otomatis hash password
    ];

    /**
     * Boot model untuk menambahkan UUID saat membuat record baru.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Jika id_pengguna belum ada, isi dengan UUID
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi: Satu pengguna memiliki satu data pelanggan.
     */
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Helper untuk cek role pengguna.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }
}
