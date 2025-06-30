<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        // Ambil data pengguna yang sedang login
        $user = Auth::user();

        // Loop melalui setiap role yang diizinkan untuk rute ini
        foreach ($roles as $role) {
            // Jika role pengguna cocok dengan salah satu role yang diizinkan, lanjutkan request
            if ($user->role == $role) {
                return $next($request);
            }
        }

        // Jika tidak ada role yang cocok, kembalikan ke halaman sebelumnya dengan error
        // atau bisa juga diarahkan ke halaman 403 (Forbidden)
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
