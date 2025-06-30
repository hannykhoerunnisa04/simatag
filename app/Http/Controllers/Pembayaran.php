<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_bukti';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'id_bukti',
        'Id_tagihan', // Sesuai dengan kolom di DB
        'tgl_bayar',
        'metode_bayar',
        'file_bukti',
        'status_validasi',
    ];

    protected $casts = [
        'tgl_bayar' => 'datetime',
    ];

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
     * Relasi ke model Tagihan.
     */
    public function tagihan()
    {
        // Diperbaiki: Foreign key disesuaikan dengan nama kolom di database ('Id_tagihan')
        return $this->belongsTo(Tagihan::class, 'Id_tagihan', 'id_tagihan');
    }
}
