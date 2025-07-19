<?php

namespace App\Http\Controllers;

use App\Models\Pemutusan; // Model Pemutusan
use App\Models\Pelanggan; // Model Pelanggan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemutusanController extends Controller
{
    /**
     * Tampilkan daftar semua data pemutusan.
     */
    public function index(Request $request)
    {
        $query = Pemutusan::with('pelanggan'); // Eager load pelanggan

        // Fitur pencarian berdasarkan ID Pelanggan
        if ($request->filled('search')) {
            $query->where('id_pelanggan', 'like', '%' . $request->search . '%');
        }

        $pemutusans = $query->orderBy('tgl_pemutusan', 'desc')->paginate(10);

        return view('pemutusan.index', compact('pemutusans'));
    }

    /**
     * Tampilkan form untuk membuat data pemutusan baru.
     */
    public function create()
    {
        // Ambil pelanggan aktif untuk dropdown
        $pelanggans = Pelanggan::where('status_pelanggan', 'aktif')
                        ->orderBy('nama_pelanggan')->get();

        // Generate ID Pemutusan otomatis
        $last = Pemutusan::orderBy('id_pemutusan', 'desc')->first();
        if (!$last) {
            $newId = 'PMT-001';
        } else {
            $num = intval(substr($last->id_pemutusan, 4)) + 1;
            $newId = 'PMT-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        return view('pemutusan.create', compact('pelanggans', 'newId'));
    }

    /**
     * Simpan data pemutusan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_pemutusan' => 'required|string|max:40|unique:pemutusan,id_pemutusan',
            'id_pelanggan' => 'required|string|exists:pelanggan,id_pelanggan',
            'tgl_pemutusan' => 'required|date',
            'alasan_pemutusan' => 'nullable|string',
            'status_pemutusan' => 'required|in:permanen,selesai',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data pemutusan
            Pemutusan::create($validatedData);

            // Update status pelanggan (kecuali jika status selesai)
            $pelanggan = Pelanggan::find($validatedData['id_pelanggan']);
            if ($pelanggan && $validatedData['status_pemutusan'] !== 'selesai') {
                $pelanggan->status_pelanggan = 'tidak aktif';
                $pelanggan->save();
            }

            DB::commit();
            return redirect()->route('admin.pemutusan.index')
                             ->with('success', 'Data pemutusan berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Gagal menyimpan data pemutusan. Error: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Tampilkan form edit data pemutusan.
     */
    public function edit(Pemutusan $pemutusan)
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        return view('pemutusan.edit', compact('pemutusan', 'pelanggans'));
    }

    /**
     * Update data pemutusan.
     */
    public function update(Request $request, Pemutusan $pemutusan)
    {
        $validatedData = $request->validate([
            'tgl_pemutusan' => 'required|date',
            'alasan_pemutusan' => 'nullable|string',
            'status_pemutusan' => 'required|in:permanen,selesai',
        ]);

        DB::beginTransaction();
        try {
            $statusSebelumnya = $pemutusan->status_pemutusan;

            $pemutusan->update($validatedData);

            $pelanggan = $pemutusan->pelanggan;

            // Jika status diubah menjadi selesai
            if ($validatedData['status_pemutusan'] == 'selesai' && $statusSebelumnya != 'selesai') {
                if ($pelanggan) {
                    $pelanggan->status_pelanggan = 'aktif';
                    $pelanggan->save();
                }
            } 
            // Jika status berubah dari selesai ke permanen
            elseif ($statusSebelumnya == 'selesai' && $validatedData['status_pemutusan'] != 'selesai') {
                if ($pelanggan) {
                    $pelanggan->status_pelanggan = 'tidak aktif';
                    $pelanggan->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.pemutusan.index')
                             ->with('success', 'Data pemutusan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Gagal memperbarui data pemutusan. Error: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Hapus data pemutusan.
     */
    public function destroy(Pemutusan $pemutusan)
    {
        DB::beginTransaction();
        try {
            $pemutusan->delete();
            DB::commit();
            return redirect()->route('admin.pemutusan.index')
                             ->with('success', 'Data pemutusan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pemutusan.index')
                             ->with('error', 'Gagal menghapus data pemutusan. Error: ' . $e->getMessage());
        }
    }
}
