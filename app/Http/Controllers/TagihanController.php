<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tagihans = Tagihan::with('pelanggan')->orderBy('tgl_jatuh_tempo', 'desc')->paginate(10);
        
        // Tambahan: Data untuk dropdown filter bulan jika diperlukan
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('tagihan.index', compact('tagihans', 'bulanOptions'));
    }

    /**
     * Menampilkan form untuk membuat resource baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        
        // Tambahan: Data bulan untuk dropdown
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Data tahun untuk dropdown
        $tahunOptions = [];
        $currentYear = date('Y');
        for ($i = $currentYear - 2; $i <= $currentYear + 2; $i++) {
            $tahunOptions[$i] = $i;
        }
        
        return view('tagihan.create', compact('pelanggans', 'bulanOptions', 'tahunOptions'));
    }

    /**
     * Menyimpan resource yang baru dibuat ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'id_tagihan' => 'required|string|max:40|unique:tagihan,id_tagihan',
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tgl_jatuh_tempo' => 'nullable|date',
            'status_tagihan' => 'required|in:lunas,belum,telat',
        ]);

        // Format periode dalam bahasa Indonesia
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $bulanTagihan = $bulanIndonesia[$validatedData['periode_bulan']];
        $periode = $bulanTagihan . ' ' . $validatedData['periode_tahun'];

        // Simpan data ke database
        Tagihan::create([
            'id_tagihan' => $validatedData['id_tagihan'],
            'id_pelanggan' => $validatedData['id_pelanggan'],
            'periode' => $periode,
            'jumlah_tagihan' => $validatedData['jumlah_tagihan'],
            'tgl_jatuh_tempo' => $validatedData['tgl_jatuh_tempo'],
            'status_tagihan' => $validatedData['status_tagihan'],
        ]);

        return redirect()->route('admin.tagihan.index')
                         ->with('success', 'Data tagihan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan resource yang spesifik.
     *
     * @param  \App\Models\Tagihan  $tagihan
     * @return \Illuminate\Http\Response
     */
    public function show(Tagihan $tagihan)
    {
        return redirect()->route('tagihan.edit', $tagihan);
    }

    /**
     * Menampilkan form untuk mengedit resource yang spesifik.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tagihan = Tagihan::where('id_tagihan', $id)->with('pelanggan')->firstOrFail();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        
        // Data bulan untuk dropdown
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // Data tahun untuk dropdown
        $tahunOptions = [];
        $currentYear = date('Y');
        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
            $tahunOptions[$i] = $i;
        }
        
        // Extract bulan dan tahun dari periode yang tersimpan
        $periodeArray = explode(' ', $tagihan->periode);
        $bulanTagihan = $periodeArray[0] ?? '';
        $tahunTagihan = $periodeArray[1] ?? date('Y');
        
        // Convert nama bulan ke angka
        $bulanAngka = array_search($bulanTagihan, $bulanOptions) ?: 1;
        
        return view('tagihan.edit', compact('tagihan', 'pelanggans', 'bulanOptions', 'tahunOptions', 'bulanTagihan', 'tahunTagihan', 'bulanAngka'));
    }

    /**
     * Memperbarui resource yang spesifik di penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::where('id_tagihan', $id)->firstOrFail();
        
        // Validasi input
        $validatedData = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:' . (date('Y') - 10) . '|max:' . (date('Y') + 5),
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tgl_jatuh_tempo' => 'nullable|date',
            'status_tagihan' => 'required|in:lunas,belum,telat',
        ]);

        // Format periode dalam bahasa Indonesia
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $bulanTagihan = $bulanIndonesia[$validatedData['periode_bulan']];
        $periode = $bulanTagihan . ' ' . $validatedData['periode_tahun'];

        // Update data
        $tagihan->update([
            'id_pelanggan' => $validatedData['id_pelanggan'],
            'periode' => $periode,
            'jumlah_tagihan' => $validatedData['jumlah_tagihan'],
            'tgl_jatuh_tempo' => $validatedData['tgl_jatuh_tempo'],
            'status_tagihan' => $validatedData['status_tagihan'],
        ]);

        return redirect()->route('admin.tagihan.index')
                         ->with('success', 'Data tagihan berhasil diperbarui.');
    }

    /**
     * Menghapus resource yang spesifik dari penyimpanan.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $tagihan = Tagihan::where('id_tagihan', $id)->firstOrFail();
            $tagihan->delete();
            
            return redirect()->route('admin.tagihan.index')
                             ->with('success', 'Data tagihan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.tagihan.index')
                             ->with('error', 'Gagal menghapus data tagihan. Error: ' . $e->getMessage());
        }
    }

    /**
     * Method tambahan untuk filter berdasarkan bulan/tahun
     */
    public function filter(Request $request)
    {
        $query = Tagihan::with('pelanggan');
        
        if ($request->filled('bulan')) {
            $bulanIndonesia = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            $bulanTagihan = $bulanIndonesia[$request->bulan];
            $query->where('periode', 'LIKE', $bulanTagihan . '%');
        }
        
        if ($request->filled('tahun')) {
            $query->where('periode', 'LIKE', '%' . $request->tahun);
        }
        
        if ($request->filled('status')) {
            $query->where('status_tagihan', $request->status);
        }
        
        $tagihans = $query->orderBy('tgl_jatuh_tempo', 'desc')->paginate(10);
        
        // Data untuk dropdown
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return view('tagihan.index', compact('tagihans', 'bulanOptions'));
    }
}