<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tagihan;
use App\Models\Pelanggan;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan milik pelanggan yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Dapatkan pengguna yang sedang login
        $user = Auth::user();

        // 2. Cari data pelanggan yang terhubung dengan pengguna tersebut
        $pelanggan = Pelanggan::where('id_pengguna', $user->id_pengguna)->first();

        // 3. Jika data pelanggan tidak ditemukan, kembalikan view dengan data kosong
        if (!$pelanggan) {
            $tagihans = collect(); // Membuat koleksi kosong
            return view('rolepelanggan.tagihan.index', compact('tagihans'));
        }

        // 4. Jika pelanggan ditemukan, ambil semua tagihan miliknya
        $tagihans = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
                            ->orderBy('tgl_jatuh_tempo', 'desc') // Urutkan berdasarkan yang paling baru
                            ->paginate(10); // Gunakan paginasi

        // 5. Kirim data tagihan ke view
        return view('rolepelanggan.tagihan.index', compact('tagihans'));
    }

    /**
     * Menampilkan detail satu tagihan spesifik.
     * Termasuk pengecekan keamanan agar pelanggan tidak bisa melihat tagihan orang lain.
     *
     * @param  \App\Models\Tagihan  $tagihan
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Tagihan $tagihan)
    {
        // Dapatkan ID pelanggan yang sedang login
        $idPelangganLogin = Auth::user()->pelanggan->id_pelanggan;

        // Keamanan: Pastikan tagihan yang diminta adalah milik pelanggan yang sedang login
        if ($tagihan->id_pelanggan !== $idPelangganLogin) {
            // Jika bukan, kembalikan ke halaman daftar tagihan dengan pesan error
            return redirect()->route('pelanggan.tagihan.index')
                             ->with('error', 'Anda tidak memiliki akses ke tagihan ini.');
        }
        
        // Jika cocok, tampilkan halaman detail tagihan
        return view('rolepelanggan.tagihan.show', compact('tagihan'));
    }

    // Untuk role pelanggan, method create, store, edit, update, dan destroy
    // biasanya tidak diperlukan karena mereka tidak mengelola tagihan sendiri.
    // Metode-metode ini bisa dibiarkan kosong atau dihapus.
}
