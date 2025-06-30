<?php

namespace App\Http\Controllers;

use App\Models\PaketLayanan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Pelanggan;

class PaketLayananController extends Controller
{
    /**
     * Menampilkan daftar semua paket layanan.
     */
    public function index(Request $request)
    {
        $query = PaketLayanan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_paket', 'like', '%' . $search . '%');
        }

        $paketLayanans = $query->orderBy('nama_paket', 'asc')->paginate(10);
        return view('paketlayanan.index', compact('paketLayanans'));
    }

    /**
     * Menampilkan form untuk membuat paket layanan baru.
     */
    public function create()
    {
        return view('paketlayanan.create');
    }

    /**
     * Menyimpan paket layanan yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_paket' => 'required|string|max:20|unique:paket_layanan,id_paket',
            'nama_paket' => 'required|string|max:255',
            'kecepatan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        PaketLayanan::create($validatedData);

        return redirect()->route('admin.paketlayanan.index')
                         ->with('success', 'Paket layanan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit paket layanan.
     */
    public function edit(PaketLayanan $paketlayanan)
    {
        return view('paketlayanan.edit', compact('paketlayanan'));
    }

    /**
     * Memperbarui data paket layanan di database.
     */
    public function update(Request $request, PaketLayanan $paketlayanan)
    {
        $validatedData = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'kecepatan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $paketlayanan->update($validatedData);

        return redirect()->route('admin.paketlayanan.index')
                         ->with('success', 'Data paket layanan berhasil diperbarui.');
    }

    /**
     * Menghapus paket layanan dari database.
     */
    public function destroy(PaketLayanan $paketlayanan)
    {
        try {
            // Cek apakah paket masih digunakan oleh pelanggan
            $pelangganCount = Pelanggan::where('id_paket', $paketlayanan->id_paket)->count();
            
            if ($pelangganCount > 0) {
                return redirect()->route('admin.paketlayanan.index')
                                 ->with('error', 'Paket layanan tidak dapat dihapus karena masih digunakan oleh ' . $pelangganCount . ' pelanggan.');
            }
            
            // Hapus paket layanan
            $paketlayanan->delete();
            
            return redirect()->route('admin.paketlayanan.index')
                             ->with('success', 'Paket layanan berhasil dihapus.');
                             
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.paketlayanan.index')
                             ->with('error', 'Paket layanan tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.paketlayanan.index')
                             ->with('error', 'Terjadi kesalahan saat menghapus paket layanan: ' . $e->getMessage());
        }
    }
}