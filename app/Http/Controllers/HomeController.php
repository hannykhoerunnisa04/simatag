<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Mengarahkan pengguna ke dashboard yang sesuai berdasarkan role mereka.
     */
    public function dashboard()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'pelanggan':
                return redirect()->route('pelanggan.dashboard');
            case 'atasan':
                return redirect()->route('atasan.dashboard');
            default:
                // Logout pengguna jika role tidak valid
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect('/login')->with('error', 'Role tidak dikenal.');
        }
    }
}

