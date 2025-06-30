<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemutusan;
use App\Models\Pelanggan;

class PemutusanController extends Controller
{
    /**
     * Menampilkan informasi pemutusan untuk pelanggan yang sedang login.
     */
    public function index()
    {
        // 1. Dapatkan pengguna yang sedang login
        $user = Auth::user();

        // 2. Cari data pelanggan yang terhubung dengan pengguna tersebut
        // Pastikan relasi antara Pengguna dan Pelanggan sudah benar
        $pelanggan = Pelanggan::where('id_pengguna', $user->id_pengguna)->first();
        
        // 3. Inisialisasi variabel pemutusan sebagai null
        $pemutusan = null;
        
        // 4. Jika data pelanggan ditemukan, cari data pemutusan yang relevan
        if ($pelanggan) {
            // Ambil data pemutusan yang id_pelanggan-nya cocok dan statusnya BUKAN 'selesai'
            $pemutusan = Pemutusan::with('pelanggan') // Eager load relasi untuk data paket
                                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                                ->where('status_pemutusan', '!=', 'selesai')
                                ->first(); // Ambil hanya satu record pemutusan yang aktif/berjalan
        }

        // 5. Kirim data (baik itu data pemutusan atau null) ke view
        return view('rolepelanggan.pemutusan.index', compact('pemutusan'));
    }
}
