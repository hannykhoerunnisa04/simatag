<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pemutusan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk pelanggan yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Inisialisasi nilai default
        $tagihanBulanIni = 0;
        $statusTagihan = 'Lunas';
        $jumlahBelumLunas = 0;
        $statusPemutusan = 'Aktif';
        $riwayatPembayaran = collect();
        $notifikasi = collect();

        $pelanggan = Pelanggan::where('id_pengguna', $user->id_pengguna)->first();

        if ($pelanggan) {
            
            $semuaTagihan = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)->get();
            $tagihanIds = $semuaTagihan->pluck('id_tagihan');

            // --- 1. Logika untuk Kartu Statistik ---
            $tagihanBulanIni = $semuaTagihan->where('status_tagihan', '!=', 'lunas')
                ->where('tgl_jatuh_tempo', '>=', Carbon::now()->startOfMonth())
                ->sum('jumlah_tagihan');

            $jumlahBelumLunas = $semuaTagihan->whereIn('status_tagihan', ['belum', 'telat'])->count();
            $statusTagihan = ($jumlahBelumLunas > 0) ? 'Belum Lunas' : 'Lunas';

            $pemutusan = Pemutusan::where('id_pelanggan', $pelanggan->id_pelanggan)
                                  ->where('status_pemutusan', '!=', 'selesai')->first();
            $statusPemutusan = $pemutusan ? ucfirst($pemutusan->status_pemutusan) : 'Aktif';

            // --- 2. Logika untuk Tabel Riwayat Pembayaran ---
            $riwayatPembayaran = Pembayaran::whereIn('Id_tagihan', $tagihanIds)
                                           ->with('tagihan')
                                           ->orderBy('tgl_bayar', 'desc')
                                           ->take(5)
                                           ->get();
                                           
            // --- 3. Diperbarui: Logika untuk Notifikasi Gabungan ---
            // Ambil pembayaran yang baru divalidasi
            $notifPembayaran = Pembayaran::whereIn('Id_tagihan', $tagihanIds)
                                        ->where('status_validasi', '!=', 'pending')
                                        ->get()
                                        ->map(function ($item) {
                                            $statusClass = strtolower($item->status_validasi) == 'valid' ? 'text-green-500' : 'text-red-500';
                                            $icon = strtolower($item->status_validasi) == 'valid' ? 'fa-check-circle' : 'fa-times-circle';
                                            return [
                                                'tanggal' => $item->tgl_bayar,
                                                'pesan' => "Pembayaran untuk tagihan <strong>{$item->Id_tagihan}</strong> telah divalidasi: <strong class='{$statusClass}'>" . strtoupper($item->status_validasi) . "</strong>",
                                                'icon' => $icon,
                                            ];
                                        });

            // Ambil tagihan baru
            $notifTagihan = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'tanggal' => $item->tgl_jatuh_tempo, // Kita urutkan berdasarkan tanggal ini
                                            'pesan' => "Tagihan baru untuk periode <strong>{$item->periode}</strong> telah dibuat.",
                                            'icon' => 'fa-file-invoice-dollar text-blue-500',
                                        ];
                                    });

            // Gabungkan semua notifikasi, urutkan berdasarkan tanggal, dan ambil 5 terbaru
            $notifikasi = $notifPembayaran->concat($notifTagihan)
                                         ->sortByDesc('tanggal')
                                         ->take(5);
        }

        // Kirim semua data yang sudah dihitung ke view
        return view('dashboards.pelanggan', compact(
            'tagihanBulanIni',
            'statusTagihan',
            'jumlahBelumLunas',
            'statusPemutusan',
            'riwayatPembayaran',
            'notifikasi' // Kirim notifikasi yang sudah digabung ke view
        ));
    }
}
