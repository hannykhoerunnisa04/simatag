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
        $query = Pelanggan::with('paket'); // Eager load relasi paket

        // Pencarian berdasarkan nama atau ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('id_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter status jika ada
        if ($request->filled('status')) {
            $query->where('status_pelanggan', $request->status);
        }

        // Urutkan dan paginasi
        $pelanggans = $query->orderBy('nama_pelanggan', 'asc')->paginate(10);

        return view('roleatasan.datapelanggan.index', compact('pelanggans'));
    }

    /**
     * Mengambil detail data pelanggan.
     */
    public function detail($id)
    {
        $pelanggan = Pelanggan::with('paket')->find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        return response()->json([
            'id_pelanggan'      => $pelanggan->id_pelanggan,
            'nama_pelanggan'    => $pelanggan->nama_pelanggan,
            'alamat'            => $pelanggan->alamat,
            'no_hp'             => $pelanggan->no_hp,
            'status_pelanggan'  => $pelanggan->status_pelanggan,
            'paket'             => $pelanggan->paket,
            'pic'               => $pelanggan->pic ?? 'Tidak ada PIC',
            'email'             => $pelanggan->email_pic ?? 'Tidak ada Email PIC',
        ]);
    }
}
