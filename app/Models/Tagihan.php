<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    // HAPUS ATAU BERI KOMENTAR METHOD DI BAWAH INI
    /*
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = 'TGH-' . strtoupper(Str::random(10));
            }
        });
    }
    */

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'Id_tagihan', 'id_tagihan');
    }
}
