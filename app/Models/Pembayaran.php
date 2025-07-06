<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'Id_pembayaran';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'Id_pembayaran',
        'Id_tagihan',
        'tgl_bayar',
        'metode_bayar',
        'file_bukti',
        'status_validasi',
    ];

    protected $casts = [
        'tgl_bayar' => 'datetime',
    ];

    /**
     * Diaktifkan kembali: Method boot() untuk membuat ID otomatis.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                // Contoh: Membuat ID unik seperti BKT- diikuti string acak
                $model->{$model->getKeyName()} = 'BKT-' . strtoupper(Str::random(12));
            }
        });
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'Id_tagihan', 'id_tagihan');
    }
}
