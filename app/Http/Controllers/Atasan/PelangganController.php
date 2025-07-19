<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    /**
     * Menampilkan daftar semua data pelanggan untuk atasan.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::with('paket'); // Eager load relasi paket untuk menampilkan nama paket

        // Logika untuk pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('id_pelanggan', 'like', "%{$search}%");
            });
        }
        
        // Logika untuk filter status (jika diperlukan)
        if ($request->filled('status')) {
            $query->where('status_pelanggan', $request->status);
        }

        // Mengurutkan berdasarkan nama pelanggan dan melakukan paginasi
$pelanggans = $query->orderBy('nama_pelanggan', 'asc')->paginate(10);

        // Mengirim data ke view
        return view('roleatasan.datapelanggan.index', compact('pelanggans'));
    }

}
