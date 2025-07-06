<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import semua controller yang dibutuhkan
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TagihanController as AdminTagihanController; 
use App\Http\Controllers\PaketLayananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ValidasiBuktiController;
use App\Http\Controllers\PemutusanController;
use App\Http\Controllers\RekapKeuanganController;

// Pelanggan Controllers
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboardController;
use App\Http\Controllers\Pelanggan\TagihanController as PelangganTagihanController; 
use App\Http\Controllers\Pelanggan\UploadBuktiController as PelangganUploadBuktiController;
use App\Http\Controllers\Pelanggan\PemutusanController as PelangganPemutusanController; 


// Atasan Controllers
use App\Http\Controllers\Atasan\DashboardController as AtasanDashboardController;
use App\Http\Controllers\Atasan\RekapKeuanganController as AtasanRekapKeuanganController;
use App\Http\Controllers\Atasan\PelangganController as AtasanPelangganController;
use App\Http\Controllers\Atasan\TagihanController as AtasanTagihanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- RUTE UNTUK TAMU (TIDAK PERLU LOGIN) ---
Route::get('/', function () {
    return redirect()->route('login');
});


// --- RUTE UNTUK SEMUA PENGGUNA YANG SUDAH LOGIN ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

     // TAMBAHKAN DUA BARIS INI
    Route::get('/password/change', [ProfileController::class, 'showChangePasswordForm'])->name('password.change.form');
    Route::put('/password/change', [ProfileController::class, 'updatePassword'])->name('password.change');
});



// --- GRUP RUTE KHUSUS UNTUK ROLE ADMIN ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengguna/next-id/{role}', [PenggunaController::class, 'getNextId'])->name('admin.pengguna.next-id');

    // Resource Management (CRUD) hanya untuk Admin
    Route::resource('pelanggan', PelangganController::class);
    Route::resource('tagihan', AdminTagihanController::class); 
    Route::resource('pengguna', PenggunaController::class);
    Route::resource('paketlayanan', PaketLayananController::class);
    Route::resource('validasibukti', ValidasiBuktiController::class);
    Route::resource('pemutusan', PemutusanController::class);
    Route::resource('rekapkeuangan', RekapKeuanganController::class);

    // ✅ Rute kustom untuk reset password pengguna
    Route::post('/pengguna/{pengguna}/reset-password', [PenggunaController::class, 'resetPassword'])->name('pengguna.resetPassword');

    // Rute kustom lainnya
    Route::post('/validasibukti/{id_bukti}/validate', [ValidasiBuktiController::class, 'validatePayment'])->name('validasibukti.validate');
});



// --- GRUP RUTE KHUSUS UNTUK ROLE PELANGGAN ---
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Diperbaiki: Semua rute pelanggan ditempatkan di dalam grup ini
    Route::resource('tagihan', PelangganTagihanController::class);
   // --- Rute untuk Upload Bukti didefinisikan secara manual ---
    Route::get('/uploadbukti', [PelangganUploadBuktiController::class, 'index'])->name('uploadbukti.index');
    Route::get('/uploadbukti/create', [PelangganUploadBuktiController::class, 'create'])->name('uploadbukti.create');
    Route::post('/uploadbukti', [PelangganUploadBuktiController::class, 'store'])->name('uploadbukti.store');
    // Parameter diubah menjadi {id_pembayaran} agar lebih jelas
    Route::get('/uploadbukti/{id_pembayaran}/edit', [PelangganUploadBuktiController::class, 'edit'])->name('uploadbukti.edit');
    Route::put('/uploadbukti/{id_pembayaran}', [PelangganUploadBuktiController::class, 'update'])->name('uploadbukti.update');
    Route::delete('/uploadbukti/{id_pembayaran}', [PelangganUploadBuktiController::class, 'destroy'])->name('uploadbukti.destroy');
    Route::get('/pemutusan', [PelangganPemutusanController::class, 'index'])->name('pemutusan.index');


});


// --- GRUP RUTE KHUSUS UNTUK ROLE ATASAN ---
Route::middleware(['auth', 'role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {
    Route::get('/dashboard', [AtasanDashboardController::class, 'index'])->name('dashboard');

    Route::get('/rekapkeuangan', [AtasanRekapKeuanganController::class, 'index'])->name('rekapkeuangan.index');
    Route::get('/datapelanggan', [AtasanPelangganController::class, 'index'])->name('datapelanggan.index');
    Route::get('/datatagihan', [AtasanTagihanController::class, 'index'])->name('datatagihan.index');

});


// --- RUTE AUTENTIKASI DARI LARAVEL BREEZE ---
// Ini harus berada di luar middleware 'auth' agar tamu bisa mengakses halaman login, register, dll.
require __DIR__.'/auth.php';

