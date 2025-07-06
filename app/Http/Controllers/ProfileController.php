<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengubah password.
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('password.change.form')->with('status', 'password-updated');
    }
    
    // Anda bisa menambahkan method edit, update, dan destroy untuk profil di sini nanti
    // public function edit(Request $request) { ... }
    // public function update(Request $request) { ... }
    // public function destroy(Request $request) { ... }
}
