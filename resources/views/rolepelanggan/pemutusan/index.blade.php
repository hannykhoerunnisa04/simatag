<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi Pemutusan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { background-color: #f0f4f8; }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    @include('components.sidebar.pelanggan-sidebar')

    <!-- Konten Utama -->
    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Informasi Pemutusan
            </h1>
            <div class="flex items-center gap-3 mt-4 sm:mt-0">
                <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                <span class="text-gray-700 text-sm font-semibold">{{ Auth::user()->nama ?? 'Pelanggan' }}</span>
            </div>
        </header>

        <!-- Notifikasi Status Pemutusan -->
        @if($pemutusan)
            <div class="mb-6 p-4 bg-orange-100 border-l-4 border-orange-500 rounded-r-lg shadow-md">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-exclamation-triangle text-orange-500 mr-3"></i></div>
                    <div>
                        <p class="font-bold text-orange-800">Pemutusan Layanan: {{ ucfirst($pemutusan->status_pemutusan) }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
             <h2 class="text-xl font-semibold p-4 border-b">Riwayat Pemutusan Layanan</h2>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-left">
                    <tr>
                        <th class="p-3 font-semibold uppercase">ID Paket Layanan</th>
                        <th class="p-3 font-semibold uppercase">Alasan Pemutusan</th>
                        <th class="p-3 font-semibold uppercase text-center">Status Pemutusan</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    {{-- Diperbaiki: Logika untuk menampilkan data atau pesan "tidak ada" --}}
                    @if ($pemutusan)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-medium">{{ $pemutusan->pelanggan->id_paket ?? 'N/A' }}</td>
                            <td class="p-3">{{ $pemutusan->alasan_pemutusan ?: 'Tidak ada alasan spesifik.' }}</td>
                            <td class="p-3 text-center">
                                @if(strtolower($pemutusan->status_pemutusan) == 'permanen')
                                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-red-100 text-red-700 uppercase">{{ $pemutusan->status_pemutusan }}</span>
                                @elseif(strtolower($pemutusan->status_pemutusan) == 'sementara')
                                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-yellow-100 text-yellow-700 uppercase">{{ $pemutusan->status_pemutusan }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-green-100 text-green-700 uppercase">{{ $pemutusan->status_pemutusan }}</span>
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="3" class="text-center p-10 text-gray-500">
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
