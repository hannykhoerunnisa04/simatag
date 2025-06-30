<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar semua data tagihan untuk atasan.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with('pelanggan'); // Eager load relasi pelanggan

        // Logika untuk pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id_tagihan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q) use ($search) {
                      $q->where('nama_pelanggan', 'like', "%{$search}%")
                        ->orWhere('id_pelanggan', 'like', "%{$search}%");
                  });
        }
        
        // Logika untuk filter status
        if ($request->filled('status')) {
            $query->where('status_tagihan', $request->status);
        }

        // Mengurutkan berdasarkan tanggal jatuh tempo terbaru dan melakukan paginasi
        $tagihans = $query->orderBy('tgl_jatuh_tempo', 'desc')->paginate(15);

        // Mengirim data ke view
        return view('roleatasan.datatagihan.index', compact('tagihans'));
    }

    // Untuk role Atasan, biasanya tidak ada fungsi show, create, edit, atau delete.
    // Metode ini hanya untuk melihat daftar.
}
