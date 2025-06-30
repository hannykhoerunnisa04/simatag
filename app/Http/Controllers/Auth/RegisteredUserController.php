<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna; // Pastikan ini adalah model Pengguna Anda
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str; // Digunakan jika Id_pengguna di-generate manual di sini,
                           // tapi lebih baik di-handle oleh boot() method di model Pengguna

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan tampilan registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Menangani permintaan registrasi yang masuk.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'], // Input form Breeze default adalah 'name'
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Pengguna::class], // Validasi unik ke tabel 'pengguna'
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Asumsi Id_pengguna (UUID) di-generate otomatis oleh boot() method di model Pengguna.
        // Jika tidak, Anda perlu meng-generate dan menyertakannya di sini, contoh:
        // 'Id_pengguna' => (string) Str::uuid(),

        $user = Pengguna::create([
            'nama' => $request->name, // Mapping 'name' dari form ke kolom 'nama' di tabel Anda
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan', // Atur role default untuk pengguna baru
            // 'email_verified_at' => now(), // Aktifkan jika ingin email langsung terverifikasi saat registrasi
                                            // Biasanya Breeze menangani ini terpisah.
        ]);

        event(new Registered($user));

        Auth::login($user);

        // ---- BARIS DEBUG PENTING ----
        // Setelah mencoba registrasi, halaman akan berhenti di sini dan menampilkan output.
        // Perhatikan baik-baik outputnya di browser Anda.
        // Ini akan membantu kita melihat apakah Auth::login() berhasil.
        // dd('DEBUG RegisteredUserController:', [
        //     'Auth::check()' => Auth::check(),
        //     'Auth::user()' => Auth::user(),
        //     'User yang baru dibuat (dari objek $user)' => $user->toArray(),
        //     'User yang baru dibuat (dari DB)' => Pengguna::find($user->Id_pengguna)?->toArray() // Ambil data terbaru dari DB, tambahkan null-safe operator
        // ]);
        // ---- AKHIR BARIS DEBUG ----

        // Baris redirect ini tidak akan dijalankan jika dd() di atas aktif.
        // Hapus atau komentari dd() di atas setelah selesai debug.
        return redirect(route('dashboard', absolute: false));
    }
}
