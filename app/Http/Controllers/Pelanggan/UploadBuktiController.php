<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\Tagihan;

class UploadBuktiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::where('id_pengguna', $user->id_pengguna)->first();
        $pembayarans = collect();
        if ($pelanggan) {
            $tagihanIds = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)->pluck('id_tagihan');
            $pembayarans = Pembayaran::whereIn('Id_tagihan', $tagihanIds)->orderBy('tgl_bayar', 'desc')->paginate(10);
        }
        return view('rolepelanggan.uploadbukti.index', compact('pembayarans'));
    }

    public function create()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::where('id_pengguna', $user->id_pengguna)->first();
        $tagihanBelumLunas = collect();
        if ($pelanggan) {
            $tagihanBelumLunas = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
                                        ->whereNotIn('status_tagihan', ['lunas'])
                                        ->doesntHave('pembayaran')
                                        ->get();
        }
        return view('rolepelanggan.uploadbukti.create', compact('tagihanBelumLunas'));
    }
    
    public function store(Request $request)
{
    // Dihapus: Validasi untuk 'Id_pembayaran' dihilangkan
    $request->validate([
        'Id_tagihan' => 'required|exists:tagihan,id_tagihan',
        'metode_bayar' => 'required|string|max:50',
        'file_bukti' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    $filePath = $request->file('file_bukti')->store('bukti_pembayaran', 'public');

    // Dihapus: 'Id_pembayaran' dihilangkan dari array create()
    // karena akan diisi otomatis oleh Model
    Pembayaran::create([
        'Id_tagihan' => $request->Id_tagihan,
        'tgl_bayar' => now(),
        'metode_bayar' => $request->metode_bayar,
        'file_bukti' => $filePath,
        'status_validasi' => 'pending',
    ]);
    
    return redirect()->route('pelanggan.uploadbukti.index')->with('success', 'Bukti pembayaran berhasil diunggah!');
}

    /**
     * PERBAIKAN: Mengambil data pembayaran secara manual berdasarkan ID.
     */
    public function edit($id_pembayaran)
    {
        $pembayaran = Pembayaran::findOrFail($id_pembayaran);

        // Keamanan: Pastikan pelanggan hanya bisa mengedit buktinya sendiri
        $pelanggan = Pelanggan::where('id_pengguna', Auth::id())->first();
        if (!$pelanggan || $pembayaran->tagihan->id_pelanggan !== $pelanggan->id_pelanggan || strtolower($pembayaran->status_validasi) !== 'pending') {
            return redirect()->route('pelanggan.uploadbukti.index')->with('error', 'Akses ditolak atau bukti sudah divalidasi.');
        }

        return view('rolepelanggan.uploadbukti.edit', compact('pembayaran'));
    }

    /**
     * PERBAIKAN: Memperbarui data pembayaran secara manual berdasarkan ID.
     */
    public function update(Request $request, $id_pembayaran)
    {
        $pembayaran = Pembayaran::findOrFail($id_pembayaran);

        // Keamanan (sama seperti method edit)
        $pelanggan = Pelanggan::where('id_pengguna', Auth::id())->first();
        if (!$pelanggan || $pembayaran->tagihan->id_pelanggan !== $pelanggan->id_pelanggan || strtolower($pembayaran->status_validasi) !== 'pending') {
            return redirect()->route('pelanggan.uploadbukti.index')->with('error', 'Akses ditolak atau bukti sudah divalidasi.');
        }

        $request->validate([
            'metode_bayar' => 'required|string|max:50',
            'file_bukti' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        
        $pembayaran->metode_bayar = $request->metode_bayar;

        if ($request->hasFile('file_bukti')) {
            if ($pembayaran->file_bukti && Storage::disk('public')->exists($pembayaran->file_bukti)) {
                Storage::disk('public')->delete($pembayaran->file_bukti);
            }
            $pembayaran->file_bukti = $request->file('file_bukti')->store('bukti_pembayaran', 'public');
        }

        $pembayaran->save();

        return redirect()->route('pelanggan.uploadbukti.index')->with('success', 'Bukti pembayaran berhasil diperbarui.');
    }

    /**
     * PERBAIKAN: Menghapus data pembayaran secara manual berdasarkan ID.
     */
    public function destroy($id_pembayaran)
    {
        $pembayaran = Pembayaran::findOrFail($id_pembayaran);
        
        // Keamanan (sama seperti method edit)
        $pelanggan = Pelanggan::where('id_pengguna', Auth::id())->first();
        if (!$pelanggan || $pembayaran->tagihan->id_pelanggan !== $pelanggan->id_pelanggan || strtolower($pembayaran->status_validasi) !== 'pending') {
            return redirect()->route('pelanggan.uploadbukti.index')->with('error', 'Akses ditolak atau bukti sudah divalidasi.');
        }

        try {
            if ($pembayaran->file_bukti && Storage::disk('public')->exists($pembayaran->file_bukti)) {
                Storage::disk('public')->delete($pembayaran->file_bukti);
            }
            $pembayaran->delete();
            return redirect()->route('pelanggan.uploadbukti.index')->with('success', 'Bukti pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pelanggan.uploadbukti.index')->with('error', 'Gagal menghapus bukti pembayaran.');
        }
    }
}
