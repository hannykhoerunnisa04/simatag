<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapKeuanganController extends Controller
{
    /**
     * Menampilkan halaman rekap keuangan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // --- Query dasar untuk mengambil data tagihan ---
        $queryLunas = Tagihan::where('status_tagihan', 'lunas');
        $queryBelumLunas = Tagihan::whereIn('status_tagihan', ['belum', 'telat']);

        // --- Terapkan filter periode jika ada ---
        if ($request->filled('bulan')) {
            $queryLunas->whereMonth('tgl_jatuh_tempo', $request->bulan);
            $queryBelumLunas->whereMonth('tgl_jatuh_tempo', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $queryLunas->whereYear('tgl_jatuh_tempo', $request->tahun);
            $queryBelumLunas->whereYear('tgl_jatuh_tempo', $request->tahun);
        }

        // --- Olah Data Rekap Lunas ---
        $rekapLunas = $queryLunas->select(
                                     DB::raw("DATE_FORMAT(tgl_jatuh_tempo, '%M %Y') as periode"),
                                     DB::raw('SUM(jumlah_tagihan) as total')
                                 )
                                 ->groupBy(DB::raw("DATE_FORMAT(tgl_jatuh_tempo, '%M %Y')"))
                                 ->orderByRaw('MIN(tgl_jatuh_tempo) DESC')
                                 ->get();

        // --- Olah Data Rekap Belum Lunas ---
        $rekapBelumLunas = $queryBelumLunas->select(
                                               DB::raw("DATE_FORMAT(tgl_jatuh_tempo, '%M %Y') as periode"),
                                               DB::raw('SUM(jumlah_tagihan) as total')
                                           )
                                           ->groupBy(DB::raw("DATE_FORMAT(tgl_jatuh_tempo, '%M %Y')"))
                                           ->orderByRaw('MIN(tgl_jatuh_tempo) DESC')
                                           ->get();
                
        // --- Hitung Total Keseluruhan ---
        $totalKeseluruhanLunas = $rekapLunas->sum('total');
        $totalKeseluruhanBelumLunas = $rekapBelumLunas->sum('total');

        // --- Kirim semua data ke view ---
        return view('roleatasan.rekapkeuangan.index', compact(
            'rekapLunas',
            'totalKeseluruhanLunas',
            'rekapBelumLunas',
            'totalKeseluruhanBelumLunas'
        ));
    }
}