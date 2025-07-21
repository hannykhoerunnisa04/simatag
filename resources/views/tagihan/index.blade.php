<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Tagihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            background-color: #f0f4f8;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="flex bg-gray-100 font-sans" x-data="{ showModal: false, modalUrl: '', modalMessage: '', showToast: false }">
    @include('components.sidebar.admin-sidebar')

    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
                Manajemen Data Tagihan
            </h1>
            <div class="flex items-center gap-3">
                <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
                <span class="text-gray-700 text-sm md:text-base">Admin</span>
            </div>
        </header>

        <!-- Tombol Aksi & Search -->
        <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <!-- Tombol Tambah Tagihan -->
                <a href="{{ route('admin.tagihan.create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-lg transition">
                    <i class="fas fa-plus-circle"></i> Tambah Tagihan
                </a>

                <!-- Tombol Generate Tagihan Otomatis -->
                <form action="{{ route('admin.tagihan.generate') }}" method="POST">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('Yakin ingin generate tagihan otomatis untuk semua pelanggan?')"
                        class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-lg transition">
                        <i class="fas fa-sync-alt"></i> Generate Tagihan
                    </button>
                </form>
            </div>

            <!-- Search -->
            <form action="{{ route('admin.tagihan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="text" name="search" placeholder="Cari ID, nama, atau periode..."
                    value="{{ request('search') }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    autocomplete="off">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Toast -->
        @if (session('success'))
            <div x-show="showToast" x-init="showToast = true; setTimeout(() => showToast = false, 3000)" x-transition
                class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div x-show="showToast" x-init="showToast = true; setTimeout(() => showToast = false, 3000)" x-transition
                class="fixed top-5 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50">
                <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Table Data -->
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-blue-600 text-white text-left rounded-t-lg">
                    <tr>
                        <th class="p-3 font-semibold uppercase">ID Tagihan</th>
                        <th class="p-3 font-semibold uppercase">Nama Pelanggan</th>
                        <th class="p-3 font-semibold uppercase">Periode</th>
                        <th class="p-3 font-semibold uppercase">Jatuh Tempo</th>
                        <th class="p-3 font-semibold uppercase text-right">Jumlah (Rp)</th>
                        <th class="p-3 font-semibold uppercase text-center">Status</th>
                        <th class="p-3 font-semibold uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse ($tagihans as $tagihan)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="p-3 font-mono text-xs">{{ $tagihan->id_tagihan }}</td>
                            <td class="p-3">{{ $tagihan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td class="p-3">{{ $tagihan->periode }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d M Y') }}</td>
                            <td class="p-3 text-right font-medium">{{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="p-3 text-center">
                                @if(strtolower($tagihan->status_tagihan) == 'lunas')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                                @elseif(strtolower($tagihan->status_tagihan) == 'telat')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.tagihan.edit', $tagihan) }}"
                                        class="text-yellow-500 hover:text-yellow-700" title="Edit"><i
                                            class="fas fa-edit"></i></a>

                                    <!-- Tombol Hapus -->
                                    <button @click="showModal = true; modalUrl='{{ route('admin.tagihan.destroy', $tagihan) }}'; modalMessage='Hapus tagihan {{ $tagihan->id_tagihan }}?';"
                                        class="text-red-500 hover:text-red-700" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-10 text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                                <p class="font-semibold">Belum ada data tagihan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $tagihans->links() }}
        </div>
    </main>

    <!-- Modal Konfirmasi -->
    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi</h2>
            <p class="text-gray-600 mb-6" x-text="modalMessage"></p>
            <div class="flex justify-end gap-2">
                <button @click="showModal = false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Batal</button>
                <form :action="modalUrl" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
