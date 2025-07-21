<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pengguna;
use App\Models\PaketLayanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelangganController extends Controller
{
    /**
     * Menampilkan daftar semua pelanggan dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::with('paket'); // Eager load relasi paket

        // Logika untuk pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('id_pelanggan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Logika untuk filter status
        if ($request->filled('status')) {
            $query->where('status_pelanggan', $request->status);
        }

        // Mengurutkan berdasarkan nama pelanggan
        $pelanggans = $query->orderBy('nama_pelanggan', 'asc')->paginate(10);

        return view('pelanggan.index', compact('pelanggans'));
    }

    /**
     * Menampilkan form untuk membuat pelanggan baru.
     */
    public function create()
    {
        // Ambil ID pengguna yang sudah terdaftar sebagai pelanggan
        $idPenggunaYangSudahJadiPelanggan = Pelanggan::pluck('id_pengguna')->all();

        // Ambil pengguna dengan role 'pelanggan' yang ID-nya BELUM ada di tabel pelanggan
        $calonPelanggan = Pengguna::where('role', 'pelanggan')
            ->whereNotIn('id_pengguna', $idPenggunaYangSudahJadiPelanggan)
            ->orderBy('nama')
            ->get();

        $paketLayanans = PaketLayanan::orderBy('nama_paket')->get();

        return view('pelanggan.create', compact('calonPelanggan', 'paketLayanans'));
    }

    /**
     * Menyimpan pelanggan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_pelanggan'      => ['required', 'string', 'unique:pelanggan,id_pelanggan'],
            'id_pengguna'       => ['required', 'string', 'exists:pengguna,id_pengguna', 'unique:pelanggan,id_pengguna'],
            'nama_pelanggan'    => ['required', 'string', 'max:255'],
            'alamat'            => ['required', 'string'],
            'no_hp'             => ['required', 'string', 'max:15'],
            'id_paket'          => ['required', 'string', 'exists:paket_layanan,id_paket'],
            'status_pelanggan'  => ['required', 'string', 'in:aktif,tidak aktif'],
            'pic'               => ['nullable', 'string', 'max:255'], // Tambahan
            'email_pic'         => ['nullable', 'string', 'email', 'max:255'], // Tambahan
        ]);

        Pelanggan::create($validatedData);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit pelanggan yang ada.
     */
    public function edit(Pelanggan $pelanggan)
    {
        $paketLayanans = PaketLayanan::orderBy('nama_paket')->get();
        return view('pelanggan.edit', compact('pelanggan', 'paketLayanans'));
    }

    /**
     * Memperbarui data pelanggan di database.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validatedData = $request->validate([
            'nama_pelanggan'    => ['required', 'string', 'max:255'],
            'alamat'            => ['required', 'string'],
            'no_hp'             => ['required', 'string', 'max:15'],
            'id_paket'          => ['required', 'string', 'exists:paket_layanan,id_paket'],
            'status_pelanggan'  => ['required', 'string', 'in:aktif,tidak aktif'],
            'pic'               => ['nullable', 'string', 'max:255'], // Tambahan
            'email_pic'         => ['nullable', 'string', 'email', 'max:255'], // Tambahan
        ]);

        $pelanggan->update($validatedData);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Menghapus pelanggan dari database.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        try {
            $pelanggan->delete();
            return redirect()->route('admin.pelanggan.index')
                ->with('success', 'Data pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pelanggan.index')
                ->with('error', 'Gagal menghapus pelanggan. Mungkin terkait dengan data lain.');
        }
    }
   public function showDetail($id)
    {
    $pelanggan = Pelanggan::with('paket')->find($id);

    if (!$pelanggan) {
        return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
    }

    return response()->json($pelanggan);
    }

}
