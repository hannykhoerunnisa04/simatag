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
        $request->validate([
            'id_pengguna' => ['required', 'string', 'max:40', 'unique:pengguna,id_pengguna'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pelanggan', 'atasan'])],
        ]);

        Pengguna::create([
            'id_pengguna' => $request->id_pengguna,
            'nama' => $request->name,
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
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('pengguna')->ignore($pengguna->id_pengguna, 'id_pengguna')],
            'role' => ['required', Rule::in(['admin', 'pelanggan', 'atasan'])],
        ]);

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
     * ✅ Mereset password ke default ('password123')
     */
    public function resetPassword(Pengguna $pengguna)
    {
        if (Auth::id() === $pengguna->id_pengguna) {
            return back()->with('error', 'Anda tidak dapat mereset password akun Anda sendiri.');
        }

        $pengguna->password = Hash::make('password123');
        $pengguna->save();

        return redirect()->route('admin.pengguna.index')->with('success', 'Password untuk pengguna ' . $pengguna->nama . ' berhasil direset ke default.');
    }
}
