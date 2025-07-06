<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi Pemutusan</title>
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
                Informasi Pemutusan
            </h1>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                <span class="text-gray-700 text-base font-medium">{{ Auth::user()->nama ?? 'Pelanggan' }}</span>
            </div>
        </header>

        <!-- Notifikasi Status -->
        @if($pemutusan)
            <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-400 rounded-lg shadow flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                <span class="font-medium text-orange-700">
                    Pemutusan Layanan: <strong>{{ ucfirst($pemutusan->status_pemutusan) }}</strong>
                </span>
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <h2 class="text-xl font-semibold px-6 py-4 border-b">Riwayat Pemutusan Layanan</h2>
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold tracking-wide">ID Paket Layanan</th>
                        <th class="px-6 py-3 text-left font-semibold tracking-wide">Alasan Pemutusan</th>
                        <th class="px-6 py-3 text-center font-semibold tracking-wide">Status Pemutusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @if ($pemutusan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $pemutusan->pelanggan->id_paket ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $pemutusan->alasan_pemutusan ?: 'Tidak ada alasan spesifik.' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if(strtolower($pemutusan->status_pemutusan) == 'permanen')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 uppercase">Permanen</span>
                                @elseif(strtolower($pemutusan->status_pemutusan) == 'sementara')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 uppercase">Sementara</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">{{ ucfirst($pemutusan->status_pemutusan) }}</span>
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="3" class="text-center py-12 text-gray-500">
                                <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                                <p class="font-semibold">Tidak ada informasi pemutusan layanan untuk Anda.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
