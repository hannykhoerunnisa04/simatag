<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapKeuanganController extends Controller
{
    /**
     * Menampilkan halaman rekap keuangan dengan data yang sudah diolah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // --- Memulai Query Dasar ---
        $queryLunas = Tagihan::where('status_tagihan', 'lunas');
        $queryBelumLunas = Tagihan::whereIn('status_tagihan', ['belum', 'telat']);

        // --- Logika Filter Periode yang Diperbaiki ---
        // Cek apakah pengguna melakukan filter
        $isFiltered = $request->has('bulan') || $request->has('tahun');

        if ($isFiltered) {
            // Jika ada filter, terapkan sesuai input pengguna
            if ($request->filled('bulan')) {
                $queryLunas->whereMonth('tgl_jatuh_tempo', $request->bulan);
                $queryBelumLunas->whereMonth('tgl_jatuh_tempo', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $queryLunas->whereYear('tgl_jatuh_tempo', $request->tahun);
                $queryBelumLunas->whereYear('tgl_jatuh_tempo', $request->tahun);
            }
        } else {
            // Jika tidak ada filter (halaman pertama kali dibuka),
            // default ke bulan dan tahun saat ini.
            $queryLunas->whereMonth('tgl_jatuh_tempo', Carbon::now()->month)
                       ->whereYear('tgl_jatuh_tempo', Carbon::now()->year);
            $queryBelumLunas->whereMonth('tgl_jatuh_tempo', Carbon::now()->month)
                           ->whereYear('tgl_jatuh_tempo', Carbon::now()->year);
        }

        // --- Menghitung Rekap ---
        // Hitung rekap per periode untuk tagihan LUNAS
        $rekapLunas = $queryLunas->select('periode', DB::raw('SUM(jumlah_tagihan) as total'))
                                 ->groupBy('periode')
                                 ->orderBy('periode', 'desc')
                                 ->get();
        
        // Hitung rekap per periode untuk tagihan BELUM LUNAS
        $rekapBelumLunas = $queryBelumLunas->select('periode', DB::raw('SUM(jumlah_tagihan) as total'))
                                           ->groupBy('periode')
                                           ->orderBy('periode', 'desc')
                                           ->get();
        
        // Hitung total keseluruhan untuk ditampilkan di footer tabel
        $totalKeseluruhanLunas = $rekapLunas->sum('total');
        $totalKeseluruhanBelumLunas = $rekapBelumLunas->sum('total');


        // Kirim semua data yang dibutuhkan ke view
        return view('rekapkeuangan.index', compact(
            'rekapLunas',
            'totalKeseluruhanLunas',
            'rekapBelumLunas',
            'totalKeseluruhanBelumLunas'
        ));
    }
}
