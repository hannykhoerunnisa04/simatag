<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tagihan;
use Illuminate\Pagination\LengthAwarePaginator;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            // ✅ Fix: Kirim paginator kosong biar links() gak error
            $emptyPaginator = new LengthAwarePaginator([], 0, 10);
            return view('rolepelanggan.tagihan.index', [
                'tagihans' => $emptyPaginator,
                'search' => $request->search
            ]);
        }

        $tagihans = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('id_tagihan', 'like', '%' . $request->search . '%')
                      ->orWhere('periode', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('tgl_jatuh_tempo', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('rolepelanggan.tagihan.index', [
            'tagihans' => $tagihans,
            'search' => $request->search
        ]);
    }

    public function show(Tagihan $tagihan)
    {
        $idPelangganLogin = Auth::user()->pelanggan->id_pelanggan ?? null;

        if ($tagihan->id_pelanggan !== $idPelangganLogin) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Anda tidak memiliki akses ke tagihan ini.');
        }

        return view('rolepelanggan.tagihan.show', compact('tagihan'));
    }
}
