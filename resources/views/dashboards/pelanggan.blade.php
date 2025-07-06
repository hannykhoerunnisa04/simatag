<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body { background-color: #f0f4f8; }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    {{-- Memanggil komponen sidebar baru untuk pelanggan --}}
    @include('components.sidebar.pelanggan-sidebar')

    <!-- Konten Utama -->
    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4">
            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard - <span class="text-blue-500">Sistem Informasi Manajemen Tagihan (SIMA-TAG)</span>
            </h1>
           <div class="relative inline-block text-left">
    <div class="flex items-center gap-2 cursor-pointer" onclick="toggleDropdown()">
        <i class="fas fa-user-circle text-2xl text-blue-600"></i>
        <span class="text-gray-700 text-sm font-semibold">{{ Auth::user()->nama ?? 'Pelanggan' }}</span>
        <i class="fas fa-chevron-down text-xs text-gray-600"></i>
    </div>
    <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-50">
        <a href="{{ route('password.change.form') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            <i class="fas fa-key mr-2"></i> Ubah Password
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </div>
</div>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('hidden');
    }
</script>

        </header>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-sky-400 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="font-semibold uppercase text-sm">Tagihan Bulan Ini</p>
                <p class="text-3xl font-bold text-right">Rp {{ number_format($tagihanBulanIni ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="font-semibold uppercase text-sm">Status Tagihan</p>
                <p class="text-3xl font-bold text-right">{{ $statusTagihan ?? 'Lunas' }}</p>
            </div>
            <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="font-semibold uppercase text-sm">Tagihan Belum Lunas</p>
                <p class="text-3xl font-bold text-right">{{ $jumlahBelumLunas ?? 0 }}</p>
            </div>
            <div class="bg-purple-500 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
                <p class="font-semibold uppercase text-sm">Status Layanan</p>
                <p class="text-3xl font-bold text-right">{{ $statusPemutusan ?? 'Aktif' }}</p>
            </div>
        </div>

        <!-- Konten Bawah -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tabel Riwayat Pembayaran -->
            <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-3">Riwayat Pembayaran Terbaru</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-600 text-white text-left">
                            <tr>
                                <th class="p-3">Tanggal Bayar</th>
                                <th class="p-3">Nominal</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-center">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatPembayaran as $pembayaran)
                                <tr class="border-b">
                                    <td class="p-3">{{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d M Y') }}</td>
                                    <td class="p-3">Rp {{ number_format($pembayaran->tagihan->jumlah_tagihan ?? 0, 0, ',', '.') }}</td>
                                    <td class="p-3 text-center">
                                        @if(strtolower($pembayaran->status_validasi) == 'valid')
                                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">VALID</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 uppercase">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($pembayaran->file_bukti)
                                            <a href="{{ asset('storage/' . $pembayaran->file_bukti) }}" target="_blank" class="text-blue-500 hover:underline">Lihat</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="p-4 text-center" colspan="4">Tidak ada riwayat pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kartu Pemberitahuan -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-3">Pemberitahuan Terbaru</h2>
                <ul class="space-y-3">
                    @forelse ($notifikasi as $notif)
                        <li class="text-sm text-gray-700 border-b pb-2">
                            <i class="fas {{ $notif['icon'] }} mr-2"></i>
                            {!! $notif['pesan'] !!}
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">Tidak ada pemberitahuan baru.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
