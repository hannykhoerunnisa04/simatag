<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Pemutusan;
use App\Models\Pengguna; // Ditambahkan
use App\Models\Pembayaran; // Ditambahkan
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk atasan.
     */
    public function index()
    {
        // --- Kartu Statistik ---
        $totalPelangganAktif = Pelanggan::where('status_pelanggan', 'aktif')->count();
        $totalTagihanBulanIni = Tagihan::whereYear('tgl_jatuh_tempo', now()->year)
                                       ->whereMonth('tgl_jatuh_tempo', now()->month)
                                       ->count();
        $totalPemutusan = Pemutusan::where('status_pemutusan', '!=', 'selesai')->count();

        // --- Data untuk Grafik ---
        $chartDataBulanan = Tagihan::select(
                                DB::raw("MONTH(tgl_jatuh_tempo) as nomor_bulan, DATE_FORMAT(tgl_jatuh_tempo, '%b') as bulan"),
                                DB::raw("COUNT(CASE WHEN status_tagihan = 'lunas' THEN 1 END) as lunas"),
                                DB::raw("COUNT(CASE WHEN status_tagihan != 'lunas' THEN 1 END) as belum_lunas")
                            )
                            ->whereYear('tgl_jatuh_tempo', now()->year)
                            ->groupBy('nomor_bulan', 'bulan')
                            ->orderBy('nomor_bulan', 'asc')
                            ->get();

        $chartDataPemasukan = Tagihan::select(
                                DB::raw("MONTH(tgl_jatuh_tempo) as nomor_bulan, DATE_FORMAT(tgl_jatuh_tempo, '%b') as bulan"),
                                DB::raw("SUM(jumlah_tagihan) as total")
                            )
                            ->where('status_tagihan', 'lunas')
                            ->whereYear('tgl_jatuh_tempo', now()->year)
                            ->groupBy('nomor_bulan', 'bulan')
                            ->orderBy('nomor_bulan', 'asc')
                            ->get();

        // --- Diperbarui: Logika untuk Notifikasi Gabungan ---
        $notifikasi = collect();

        // 1. Ambil pembayaran yang menunggu validasi oleh admin
        $pembayaranPending = Pembayaran::where('status_validasi', 'pending')->with('tagihan.pelanggan')->latest('tgl_bayar')->take(3)->get();
        foreach ($pembayaranPending as $p) {
            $namaPelanggan = $p->tagihan->pelanggan->nama_pelanggan ?? 'Pelanggan';
            $notifikasi->push([
                'tanggal' => $p->tgl_bayar,
                'pesan' => "Pembayaran dari <strong>{$namaPelanggan}</strong> menunggu validasi.",
                'icon' => 'fa-file-invoice text-blue-500',
                'url' => route('admin.validasibukti.index')
            ]);
        }

        // 2. Ambil pengguna baru yang ditambahkan oleh admin
        $penggunaBaru = Pengguna::latest()->take(3)->get(); // Menggunakan 'created_at' default dari model Pengguna
        foreach ($penggunaBaru as $u) {
            $notifikasi->push([
                'tanggal' => $u->created_at,
                'pesan' => "Pengguna baru ({$u->role}) telah ditambahkan: <strong>{$u->nama}</strong>.",
                'icon' => 'fa-user-plus text-green-500',
                'url' => route('admin.pengguna.index')
            ]);
        }
        
        // Urutkan semua notifikasi berdasarkan tanggal dan ambil 5 terbaru
        $notifikasi = $notifikasi->sortByDesc('tanggal')->take(5);


        return view('dashboards.atasan', compact(
            'totalPelangganAktif',
            'totalTagihanBulanIni',
            'totalPemutusan',
            'chartDataBulanan',
            'chartDataPemasukan',
            'notifikasi' // Kirim notifikasi ke view
        ));
    }
}
