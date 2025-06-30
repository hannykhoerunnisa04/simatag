<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ini tidak memiliki tabel database sendiri.
 * Digunakan sebagai representasi atau untuk logika bisnis
 * terkait rekapitulasi keuangan.
 */
class RekapKeuangan extends Model
{
    use HasFactory;

    /**
     * Karena model ini tidak memiliki tabel, kita bisa menonaktifkan
     * beberapa fitur Eloquent untuk mencegah error.
     * * @var bool
     */
    public $timestamps = false;
    protected $guarded = [];

}
