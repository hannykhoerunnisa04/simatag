<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Tagihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { background-color: #f0f4f8; }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    {{-- Memanggil komponen sidebar untuk pelanggan --}}
    @include('components.sidebar.pelanggan-sidebar')

    <!-- Konten Utama -->
    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Daftar Tagihan Anda
            </h1>
            <div class="flex items-center gap-3 mt-4 sm:mt-0">
                <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                <span class="text-gray-700 text-sm font-semibold">{{ Auth::user()->nama ?? 'Pelanggan' }}</span>
            </div>
        </header>

        <!-- Form Pencarian -->
        <div class="mb-6">
            <form action="{{ route('pelanggan.tagihan.index') }}" method="GET" class="w-full max-w-sm">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <button type="submit" class="p-1 focus:outline-none focus:shadow-outline">
                            <i class="fas fa-search text-gray-400"></i>
                        </button>
                    </span>
                    <input type="text" name="search" placeholder="Cari berdasarkan periode..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </form>
        </div>

        <!-- Notifikasi -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-r-lg" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-r-lg" role="alert">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-left">
                    <tr>
                        {{-- Diperbaiki: Ditambahkan kelas lebar (w-*) untuk merapikan --}}
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider w-1/4">Periode</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider text-right w-1/4">Jumlah</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider w-1/4">Jatuh Tempo</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider text-center w-1/4">Status</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider text-center w-auto">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse ($tagihans as $tagihan)
                        <tr class="hover:bg-blue-50">
                            <td class="px-5 py-4 whitespace-nowrap">{{ $tagihan->periode }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-right font-medium">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d F Y') }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if(strtolower($tagihan->status_tagihan) == 'lunas')
                                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-green-100 text-green-700 uppercase">LUNAS</span>
                                @elseif(strtolower($tagihan->status_tagihan) == 'telat')
                                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-orange-100 text-orange-700 uppercase">TELAT</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-red-100 text-red-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                {{-- Aksi untuk pelanggan (misal: bayar atau lihat detail) --}}
                                @if(strtolower($tagihan->status_tagihan) != 'lunas')
                                    <a href="{{ route('pelanggan.uploadbukti.index') }}" class="bg-green-600 text-white px-3 py-1.5 rounded-md shadow hover:bg-green-700 text-xs font-semibold">
                                        Sudah Bayar? Upload Bukti
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-10 text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                                <p class="font-semibold">Tidak ada data tagihan untuk ditampilkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- <div class="mt-6">
            {{ $tagihans->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div> --}}
    </main>
</body>
</html>
