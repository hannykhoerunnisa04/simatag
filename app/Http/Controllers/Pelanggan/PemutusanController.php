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

        // 2. Cari data pelanggan yang terhubung
        $pelanggan = Pelanggan::where('id_pengguna', $user->id_pengguna)->first();
        
        // 3. Inisialisasi variabel pemutusan
        $pemutusan = null;
        
        // 4. Jika pelanggan ditemukan, cari data pemutusan yang relevan
        if ($pelanggan) {
            $pemutusan = Pemutusan::with('pelanggan')
                                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                                ->first(); // Ambil hanya satu record pemutusan yang aktif
        }

        // 5. Kirim data ke view
        return view('rolepelanggan.pemutusan.index', compact('pemutusan'));
    }
}
