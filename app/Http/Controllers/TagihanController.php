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
        for ($i = $currentYear; $i <= $currentYear + 2; $i++) {
            $tahunOptions[$i] = $i;
        }

        return view('tagihan.create', compact('pelanggans', 'bulanOptions', 'tahunOptions'));
    }

    /**
     * Simpan tagihan baru dengan ID otomatis & validasi duplikat.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_pelanggan'   => 'required|exists:pelanggan,id_pelanggan',
            'periode_bulan'  => 'required|integer|min:1|max:12',
            'periode_tahun'  => 'required|integer',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tgl_jatuh_tempo'=> 'nullable|date',
            'status_tagihan' => 'required|in:lunas,belum,telat',
        ]);

        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periode = $bulanIndonesia[$validatedData['periode_bulan']] . ' ' . $validatedData['periode_tahun'];

        // ✅ Cek duplikat untuk pelanggan & periode
        $duplicate = Tagihan::where('id_pelanggan', $validatedData['id_pelanggan'])
            ->where('periode', $periode)
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'periode' => 'Tagihan untuk pelanggan ini di periode ' . $periode . ' sudah ada.'
            ])->withInput();
        }

        // ✅ Cari nomor terbesar di DB untuk ID otomatis
        $lastNumber = Tagihan::selectRaw('MAX(CAST(SUBSTRING(id_tagihan, 5) AS UNSIGNED)) as max_number')
                             ->value('max_number') ?? 0;

        $nextIdTagihan = 'TGH-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // ✅ Simpan data
        Tagihan::create([
            'id_tagihan'     => $nextIdTagihan,
            'id_pelanggan'   => $validatedData['id_pelanggan'],
            'periode'        => $periode,
            'jumlah_tagihan' => $validatedData['jumlah_tagihan'],
            'tgl_jatuh_tempo'=> $validatedData['tgl_jatuh_tempo'],
            'status_tagihan' => $validatedData['status_tagihan'],
        ]);

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan baru berhasil ditambahkan.');
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
        for ($i = $currentYear - 2; $i <= $currentYear + 2; $i++) {
            $tahunOptions[$i] = $i;
        }

        $periodeArray = explode(' ', $tagihan->periode);
        $bulanTagihan = $periodeArray[0] ?? '';
        $tahunTagihan = $periodeArray[1] ?? date('Y');
        $bulanAngka = array_search($bulanTagihan, $bulanOptions) ?: 1;

        return view('tagihan.edit', compact('tagihan', 'pelanggans', 'bulanOptions', 'tahunOptions', 'bulanTagihan', 'tahunTagihan', 'bulanAngka'));
    }

    /**
     * Update data tagihan dengan validasi duplikat.
     */
    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::where('id_tagihan', $id)->firstOrFail();

        $validatedData = $request->validate([
            'id_pelanggan'   => 'required|exists:pelanggan,id_pelanggan',
            'periode_bulan'  => 'required|integer|min:1|max:12',
            'periode_tahun'  => 'required|integer',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tgl_jatuh_tempo'=> 'nullable|date',
            'status_tagihan' => 'required|in:lunas,belum,telat',
        ]);

        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periode = $bulanIndonesia[$validatedData['periode_bulan']] . ' ' . $validatedData['periode_tahun'];

        // ✅ Cek duplikat (kecuali dirinya sendiri)
        $duplicate = Tagihan::where('id_pelanggan', $validatedData['id_pelanggan'])
            ->where('periode', $periode)
            ->where('id_tagihan', '!=', $tagihan->id_tagihan)
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'periode' => 'Tagihan untuk pelanggan ini di periode ' . $periode . ' sudah ada.'
            ])->withInput();
        }

        $tagihan->update([
            'id_pelanggan'   => $validatedData['id_pelanggan'],
            'periode'        => $periode,
            'jumlah_tagihan' => $validatedData['jumlah_tagihan'],
            'tgl_jatuh_tempo'=> $validatedData['tgl_jatuh_tempo'],
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
