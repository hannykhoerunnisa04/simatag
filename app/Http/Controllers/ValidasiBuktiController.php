<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran; // Asumsi Anda memiliki model Pembayaran
use App\Models\Tagihan;    // Asumsi Anda memiliki model Tagihan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidasiBuktiController extends Controller
{
    /**
     * Menampilkan halaman daftar bukti pembayaran yang perlu divalidasi.
     */
    public function index()
    {
        // Diperbaiki: Mengurutkan berdasarkan 'tgl_bayar' agar sesuai dengan database
        $pembayarans = Pembayaran::where('status_validasi', 'pending')
                                ->orderBy('tgl_bayar', 'asc')
                                ->paginate(10);

        // Mengirim data ke view
        return view('validasibukti.index', compact('pembayarans'));
    }

    /**
     * Memproses validasi bukti pembayaran (diterima atau ditolak).
     */
    public function validatePayment(Request $request, $id_bukti)
    {
        // Validasi input status
        $request->validate([
            'status' => 'required|in:valid,tidak valid',
        ]);

        // Memulai transaksi database untuk memastikan integritas data
        DB::beginTransaction();

        try {
            // Cari bukti pembayaran berdasarkan ID
            $pembayaran = Pembayaran::findOrFail($id_bukti);

            // Update status validasi pada bukti pembayaran
            $pembayaran->status_validasi = $request->status;
            $pembayaran->save();

            // Jika bukti pembayaran dinyatakan "valid"
            if ($request->status == 'valid') {
                // Cari tagihan yang terkait
                $tagihan = Tagihan::findOrFail($pembayaran->Id_tagihan);
                // Update status tagihan menjadi "lunas"
                $tagihan->status_tagihan = 'lunas';
                $tagihan->save();
            }
            
            // Jika semua proses berhasil, commit transaksi
            DB::commit();

            return redirect()->route('admin.validasibukti.index')
                             ->with('success', 'Status bukti pembayaran berhasil diperbarui.');

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua perubahan
            DB::rollBack();

            // Kembali ke halaman sebelumnya dengan pesan error
            return redirect()->route('admin.validasibukti.index')
                             ->with('error', 'Gagal memvalidasi bukti pembayaran. Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
