<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(Request $request)
    {
        $query = Pengguna::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $penggunas = $query->latest('created_at')->paginate(10);
        
        return view('pengguna.index', compact('penggunas'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        return view('pengguna.create');
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        // Diperbaiki: Validasi sekarang memeriksa input 'name', bukan 'nama'
        $request->validate([
            'id_pengguna' => ['required', 'string', 'max:40', 'unique:pengguna,id_pengguna'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pelanggan', 'atasan'])],
        ]);

        // Diperbaiki: Mengambil data dari 'name' dan menyimpannya ke kolom 'nama'
        Pengguna::create([
            'id_pengguna' => $request->id_pengguna,
            'nama' => $request->name, // Menggunakan $request->name
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }
    
    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(Pengguna $pengguna)
    {
        return view('pengguna.edit', compact('pengguna'));
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, Pengguna $pengguna)
    {
        // Diperbaiki: Validasi memeriksa 'nama' dari form edit
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('pengguna')->ignore($pengguna->id_pengguna, 'id_pengguna')],
            'role' => ['required', Rule::in(['admin', 'pelanggan', 'atasan'])],
        ]);

        // Diperbaiki: Menyimpan data ke kolom 'nama'
        $pengguna->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(Pengguna $pengguna)
    {
        if (Auth::id() === $pengguna->id_pengguna) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
    
    /**
     * Mereset password untuk pengguna yang spesifik.
     */
    public function resetPassword(Request $request, Pengguna $pengguna)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $pengguna->password = Hash::make($request->password);
        $pengguna->save();

        return redirect()->route('admin.pengguna.index')->with('success', 'Password untuk pengguna ' . $pengguna->nama . ' berhasil direset.');
    }
}
