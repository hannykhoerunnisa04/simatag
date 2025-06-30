<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Pengguna;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data statistik dan notifikasi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // --- Menghitung Data untuk Kartu Statistik ---
        $tagihanBulanIni = Tagihan::whereYear('tgl_jatuh_tempo', Carbon::now()->year)
                                  ->whereMonth('tgl_jatuh_tempo', Carbon::now()->month)
                                  ->count();
        $tagihanLunas = Tagihan::where('status_tagihan', 'lunas')->count();
        $tagihanBelumLunas = Tagihan::whereIn('status_tagihan', ['belum', 'telat'])->count();
        $pelangganAktif = Pelanggan::where('status_pelanggan', 'aktif')->count();

        // --- Mengambil Data untuk Tabel ---
        $tagihans = Tagihan::with('pelanggan')->latest('tgl_jatuh_tempo')->take(5)->get();
        $pelanggans = Pelanggan::latest('id_pelanggan')->take(5)->get();

        // --- Diperbarui: Logika untuk Notifikasi Gabungan ---
        $notifikasi = collect();

        // 1. Ambil pembayaran yang menunggu validasi
        $pembayaranPending = Pembayaran::where('status_validasi', 'pending')->with('tagihan.pelanggan')->get();
        foreach ($pembayaranPending as $p) {
            $namaPelanggan = $p->tagihan->pelanggan->nama_pelanggan ?? 'Pelanggan';
            $notifikasi->push([
                'tanggal' => $p->tgl_bayar,
                'pesan' => "Bukti pembayaran baru dari <strong>{$namaPelanggan}</strong> perlu divalidasi.",
                'icon' => 'fa-file-invoice text-blue-500',
                'url' => route('admin.validasibukti.index') // Link ke halaman validasi
            ]);
        }

        // 2. Ambil pengguna baru yang terdaftar
        $penggunaBaru = Pengguna::latest()->take(3)->get();
        foreach ($penggunaBaru as $u) {
            $notifikasi->push([
                'tanggal' => $u->created_at,
                'pesan' => "Pengguna baru telah terdaftar: <strong>{$u->nama}</strong>.",
                'icon' => 'fa-user-plus text-green-500',
                'url' => route('admin.pengguna.index') // Link ke halaman pengguna
            ]);
        }
        
        // Urutkan semua notifikasi berdasarkan tanggal dan ambil 5 terbaru
        $notifikasi = $notifikasi->sortByDesc('tanggal')->take(5);


        // Mengirim semua data yang sudah dihitung dan diambil ke view
        return view('dashboards.admin', compact(
            'tagihanBulanIni',
            'tagihanLunas',
            'tagihanBelumLunas',
            'pelangganAktif',
            'tagihans',
            'pelanggans',
            'notifikasi' // Kirim notifikasi ke view
        ));
    }
}
