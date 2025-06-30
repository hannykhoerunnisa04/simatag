<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    
    /**
     * Diperbaiki: Primary Key disesuaikan dengan nama kolom di database.
     */
    protected $primaryKey = 'Id_pembayaran';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'Id_pembayaran', // Diperbaiki
        'Id_tagihan',
        'tgl_bayar',
        'metode_bayar',
        'file_bukti',
        'status_validasi',
    ];

    protected $casts = [
        'tgl_bayar' => 'datetime',
    ];

    // Method boot() ini tidak lagi diperlukan jika Anda mengisi ID secara manual
    /*
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = 'BKT-' . strtoupper(Str::random(12));
            }
        });
    }
    */

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'Id_tagihan', 'id_tagihan');
    }
}
