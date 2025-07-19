<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Pemutusan;
use App\Models\Pengguna;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk atasan dengan filter tahun.
     */
    public function index(Request $request)
    {
        // Ambil tahun dari query string atau default ke tahun sekarang
        $tahunTagihan = $request->query('tahun_tagihan', now()->year);
        $tahunPemasukan = $request->query('tahun_pemasukan', now()->year);

        // --- Kartu Statistik ---
        $totalPelangganAktif = Pelanggan::where('status_pelanggan', 'aktif')->count();
        $totalTagihanBulanIni = Tagihan::whereYear('tgl_jatuh_tempo', now()->year)
                                       ->whereMonth('tgl_jatuh_tempo', now()->month)
                                       ->count();
        $totalPemutusan = Pemutusan::where('status_pemutusan', '!=', 'selesai')->count();

        // --- Data untuk Grafik Tagihan ---
        $chartDataBulanan = Tagihan::select(
                                DB::raw("MONTH(tgl_jatuh_tempo) as nomor_bulan, DATE_FORMAT(tgl_jatuh_tempo, '%b') as bulan"),
                                DB::raw("COUNT(CASE WHEN status_tagihan = 'lunas' THEN 1 END) as lunas"),
                                DB::raw("COUNT(CASE WHEN status_tagihan != 'lunas' THEN 1 END) as belum_lunas")
                            )
                            ->whereYear('tgl_jatuh_tempo', $tahunTagihan)
                            ->groupBy('nomor_bulan', 'bulan')
                            ->orderBy('nomor_bulan', 'asc')
                            ->get();

        // --- Data untuk Grafik Pemasukan ---
        $chartDataPemasukan = Tagihan::select(
                                DB::raw("MONTH(tgl_jatuh_tempo) as nomor_bulan, DATE_FORMAT(tgl_jatuh_tempo, '%b') as bulan"),
                                DB::raw("SUM(jumlah_tagihan) as total")
                            )
                            ->where('status_tagihan', 'lunas')
                            ->whereYear('tgl_jatuh_tempo', $tahunPemasukan)
                            ->groupBy('nomor_bulan', 'bulan')
                            ->orderBy('nomor_bulan', 'asc')
                            ->get();

        // --- Notifikasi Gabungan ---
        $notifikasi = collect();

        // 1. Pembayaran yang menunggu validasi
        $pembayaranPending = Pembayaran::where('status_validasi', 'pending')
                                       ->with('tagihan.pelanggan')
                                       ->latest('tgl_bayar')
                                       ->take(3)
                                       ->get();
        foreach ($pembayaranPending as $p) {
            $namaPelanggan = $p->tagihan->pelanggan->nama_pelanggan ?? 'Pelanggan';
            $notifikasi->push([
                'tanggal' => $p->tgl_bayar,
                'pesan' => "Pembayaran dari <strong>{$namaPelanggan}</strong> menunggu validasi.",
                'icon' => 'fa-file-invoice text-blue-500',
                'url' => route('admin.validasibukti.index')
            ]);
        }

        // 2. Pengguna baru yang ditambahkan
        $penggunaBaru = Pengguna::latest()->take(3)->get();
        foreach ($penggunaBaru as $u) {
            $notifikasi->push([
                'tanggal' => $u->created_at,
                'pesan' => "Pengguna baru ({$u->role}) telah ditambahkan: <strong>{$u->nama}</strong>.",
                'icon' => 'fa-user-plus text-green-500',
                'url' => route('admin.pengguna.index')
            ]);
        }

        // Urutkan notifikasi dan ambil 5 terbaru
        $notifikasi = $notifikasi->sortByDesc('tanggal')->take(5);

        return view('dashboards.atasan', compact(
            'totalPelangganAktif',
            'totalTagihanBulanIni',
            'totalPemutusan',
            'chartDataBulanan',
            'chartDataPemasukan',
            'tahunTagihan',
            'tahunPemasukan',
            'notifikasi'
        ));
    }
}
