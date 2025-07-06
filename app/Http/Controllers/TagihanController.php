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
     * Menampilkan daftar tagihan.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with('pelanggan')->orderBy('tgl_jatuh_tempo', 'desc');

        // Search berdasarkan nama pelanggan atau ID tagihan
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('pelanggan', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pelanggan', 'like', '%' . $search . '%');
                })->orWhere('id_tagihan', 'like', '%' . $search . '%');
            });
        }

        $tagihans = $query->paginate(10)->appends($request->only('search'));

        // Dropdown filter bulan (jika diperlukan di view)
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('tagihan.index', compact('tagihans', 'bulanOptions'));
    }

    /**
     * Tampilkan form untuk membuat tagihan baru.
     */
    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();

        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunOptions = [];
        $currentYear = date('Y');
        for ($i = $currentYear - 2; $i <= $currentYear + 2; $i++) {
            $tahunOptions[$i] = $i;
        }

        return view('tagihan.create', compact('pelanggans', 'bulanOptions', 'tahunOptions'));
    }

    /**
     * Simpan tagihan baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_tagihan' => 'required|string|max:40|unique:tagihan,id_tagihan',
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tgl_jatuh_tempo' => 'nullable|date',
            'status_tagihan' => 'required|in:lunas,belum,telat',
        ]);

        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $periode = $bulanIndonesia[$validatedData['periode_bulan']] . ' ' . $validatedData['periode_tahun'];

        Tagihan::create([
            'id_tagihan' => $validatedData['id_tagihan'],
            'id_pelanggan' => $validatedData['id_pelanggan'],
            'periode' => $periode,
            'jumlah_tagihan' => $validatedData['jumlah_tagihan'],
            'tgl_jatuh_tempo' => $validatedData['tgl_jatuh_tempo'],
            'status_tagihan' => $validatedData['status_tagihan'],
        ]);

        return redirect()->route('admin.tagihan.index')->with('success', 'Data tagihan baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit tagihan.
     */
    public function edit($id)
    {
        $tagihan = Tagihan::where('id_tagihan', $id)->with('pelanggan')->firstOrFail();
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();

        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunOptions = [];
        $currentYear = date('Y');
        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
            $tahunOptions[$i] = $i;
        }

        $periodeArray = explode(' ', $tagihan->periode);
        $bulanTagihan = $periodeArray[0] ?? '';
        $tahunTagihan = $periodeArray[1] ?? date('Y');
        $bulanAngka = array_search($bulanTagihan, $bulanOptions) ?: 1;

        return view('tagihan.edit', compact('tagihan', 'pelanggans', 'bulanOptions', 'tahunOptions', 'bulanTagihan', 'tahunTagihan', 'bulanAngka'));
    }

    /**
     * Update data tagihan.
     */
    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::where('id_tagihan', $id)->firstOrFail();

        $validatedData = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tgl_jatuh_tempo' => 'nullable|date',
            'status_tagihan' => 'required|in:lunas,belum,telat',
        ]);

        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $periode = $bulanIndonesia[$validatedData['periode_bulan']] . ' ' . $validatedData['periode_tahun'];

        $tagihan->update([
            'id_pelanggan' => $validatedData['id_pelanggan'],
            'periode' => $periode,
            'jumlah_tagihan' => $validatedData['jumlah_tagihan'],
            'tgl_jatuh_tempo' => $validatedData['tgl_jatuh_tempo'],
            'status_tagihan' => $validatedData['status_tagihan'],
        ]);

        return redirect()->route('admin.tagihan.index')->with('success', 'Data tagihan berhasil diperbarui.');
    }

    /**
     * Hapus tagihan.
     */
    public function destroy($id)
    {
        $tagihan = Tagihan::where('id_tagihan', $id)->firstOrFail();
        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')->with('success', 'Data tagihan berhasil dihapus.');
    }
}
