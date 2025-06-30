<?php

namespace App\Http\Controllers;

use App\Models\Pemutusan; // Pastikan model Pemutusan sudah dibuat
use App\Models\Pelanggan;  // Digunakan untuk relasi dan update status
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemutusanController extends Controller
{
    /**
     * Menampilkan daftar semua data pemutusan.
     */
    public function index(Request $request)
    {
        $query = Pemutusan::with('pelanggan'); // Eager load untuk mengambil nama pelanggan

        // Logika untuk pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id_pelanggan', 'like', '%' . $search . '%');
        }

        $pemutusans = $query->orderBy('tgl_pemutusan', 'desc')->paginate(10);

        return view('pemutusan.index', compact('pemutusans'));
    }

    /**
     * Menampilkan form untuk membuat data pemutusan baru.
     */
    public function create()
    {
        // Mengambil pelanggan yang statusnya masih 'aktif'
        $pelanggans = Pelanggan::where('status_pelanggan', 'aktif')->orderBy('nama_pelanggan')->get();
        return view('pemutusan.create', compact('pelanggans'));
    }

    /**
     * Menyimpan data pemutusan baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari form
        $validatedData = $request->validate([
            'id_pemutusan' => 'required|string|max:40|unique:pemutusan,id_pemutusan',
            'id_pelanggan' => 'required|string|exists:pelanggan,id_pelanggan',
            'tgl_pemutusan' => 'required|date',
            'alasan_pemutusan' => 'nullable|string',
            'status_pemutusan' => 'required|in:permanen,sementara,selesai',
        ]);

        // Menggunakan transaksi database untuk memastikan kedua operasi berhasil
        DB::beginTransaction();
        try {
            // 1. Buat data pemutusan baru
            Pemutusan::create($validatedData);

            // 2. Update status pelanggan menjadi 'tidak aktif' jika pemutusan bukan 'selesai'
            $pelanggan = Pelanggan::find($validatedData['id_pelanggan']);
            if ($pelanggan && $validatedData['status_pemutusan'] !== 'selesai') {
                $pelanggan->status_pelanggan = 'tidak aktif';
                $pelanggan->save();
            }

            DB::commit(); // Simpan perubahan jika semua berhasil

            return redirect()->route('admin.pemutusan.index')
                             ->with('success', 'Data pemutusan berhasil ditambahkan dan status pelanggan telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua operasi jika terjadi error
            return redirect()->back()
                             ->with('error', 'Gagal menyimpan data pemutusan. Error: ' . $e->getMessage())
                             ->withInput();
        }
    }
    
    /**
     * Menampilkan form untuk mengedit data pemutusan.
     */
    public function edit(Pemutusan $pemutusan)
    {
        // Mengirim data pemutusan yang akan diedit ke view
        return view('pemutusan.edit', compact('pemutusan'));
    }

    /**
     * Memperbarui data pemutusan di database.
     */
    public function update(Request $request, Pemutusan $pemutusan)
    {
        // Validasi data yang masuk dari form
        $validatedData = $request->validate([
            'tgl_pemutusan' => 'required|date',
            'alasan_pemutusan' => 'nullable|string',
            'status_pemutusan' => 'required|in:permanen,sementara,selesai',
        ]);

        DB::beginTransaction();
        try {
            $statusSebelumnya = $pemutusan->status_pemutusan;
            
            // Update data pemutusan
            $pemutusan->update($validatedData);

            // Jika status pemutusan diubah menjadi 'selesai'
            if ($validatedData['status_pemutusan'] == 'selesai' && $statusSebelumnya != 'selesai') {
                $pelanggan = $pemutusan->pelanggan;
                if ($pelanggan) {
                    $pelanggan->status_pelanggan = 'aktif';
                    $pelanggan->save();
                }
            } 
            // Jika status diubah DARI 'selesai' menjadi status lain (sementara/permanen)
            else if ($statusSebelumnya == 'selesai' && $validatedData['status_pemutusan'] != 'selesai') {
                $pelanggan = $pemutusan->pelanggan;
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
     * Menghapus data pemutusan dari database.
     */
    public function destroy(Pemutusan $pemutusan)
    {
        try {
            $pemutusan->delete();
            return redirect()->route('admin.pemutusan.index')
                             ->with('success', 'Data pemutusan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pemutusan.index')
                             ->with('error', 'Gagal menghapus data pemutusan.');
        }
    }
}
