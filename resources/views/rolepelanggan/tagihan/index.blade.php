<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Tagihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { background-color: #f0f4f8; }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    {{-- Sidebar --}}
    @include('components.sidebar.pelanggan-sidebar')

    <!-- Main Content -->
    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Daftar Tagihan Anda
            </h1>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                <span class="text-gray-700 text-base font-medium">{{ Auth::user()->nama ?? 'Pelanggan' }}</span>
            </div>
        </header>

        <!-- Search Box -->
        <div class="bg-white shadow rounded-lg p-4 mb-6 flex flex-col sm:flex-row items-center gap-4">
            <form action="{{ route('pelanggan.tagihan.index') }}" method="GET" class="flex w-full sm:w-auto gap-2">
                <input type="text" name="search" placeholder="Cari berdasarkan periode..." value="{{ request('search') }}"
                       class="w-full sm:w-72 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       autocomplete="off">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Notifications -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold tracking-wide">Periode</th>
                        <th class="px-6 py-3 text-right font-semibold tracking-wide">Jumlah</th>
                        <th class="px-6 py-3 text-left font-semibold tracking-wide">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-center font-semibold tracking-wide">Status</th>
                        <th class="px-6 py-3 text-center font-semibold tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tagihans as $tagihan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $tagihan->periode }}</td>
                            <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d F Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if(strtolower($tagihan->status_tagihan) == 'lunas')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">LUNAS</span>
                                @elseif(strtolower($tagihan->status_tagihan) == 'telat')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-700">TELAT</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">BELUM</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(strtolower($tagihan->status_tagihan) != 'lunas')
                                    <a href="{{ route('pelanggan.uploadbukti.index') }}" class="inline-block bg-green-600 text-white px-3 py-1.5 rounded shadow hover:bg-green-700 text-xs font-semibold whitespace-nowrap">
                                        Sudah Bayar? Upload Bukti
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                                <p class="font-semibold">Tidak ada data tagihan untuk ditampilkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $tagihans->links('vendor.pagination.tailwind') }}
        </div>
    </main>
</body>
</html>
