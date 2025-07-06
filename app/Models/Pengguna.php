<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Uncomment jika pakai verifikasi email
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
     * Karena id_pengguna bertipe varchar (UUID), maka false.
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
        'id_pengguna',   // UUID akan diisi otomatis jika kosong
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
        'password' => 'hashed', // Laravel otomatis hash password saat create/update
    ];

    /**
     * Boot method untuk model.
     * Membuat UUID otomatis jika id_pengguna kosong saat create.
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
     * Relasi: Satu pengguna memiliki satu data pelanggan.
     */
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Cek apakah pengguna ini memiliki role tertentu.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }
}
