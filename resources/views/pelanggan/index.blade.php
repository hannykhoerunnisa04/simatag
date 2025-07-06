<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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

<body class="flex bg-gray-100 font-sans" x-data="{ showModal: false, pelangganId: null, showToast: false }">
    @include('components.sidebar.admin-sidebar')

    <main class="md:ml-64 flex-1 p-6">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Data Pelanggan</h1>
        </header>

        {{-- Toast Alert Success --}}
        @if (session('success'))
            <div x-show="showToast" x-init="showToast = true; setTimeout(() => showToast = false, 3000)"
                x-transition
                class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.pelanggan.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                    <i class="fas fa-user-plus mr-2"></i>Tambah Pelanggan
                </a>
                <form action="{{ route('admin.pelanggan.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" placeholder="Cari..." value="{{ request('search') }}"
                        class="px-4 py-2 border rounded-lg">
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-blue-600 text-white text-left">
                    <tr>
                        <th class="p-3">ID Pelanggan</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Alamat</th>
                        <th class="p-3">No. HP</th>
                        <th class="p-3">Paket</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($pelanggans as $pelanggan)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-mono text-xs">{{ $pelanggan->id_pelanggan }}</td>
                            <td class="p-3 font-medium">{{ $pelanggan->nama_pelanggan }}</td>
                            <td class="p-3 max-w-xs truncate">{{ $pelanggan->alamat }}</td>
                            <td class="p-3">{{ $pelanggan->no_hp }}</td>
                            <td class="p-3">{{ $pelanggan->paket->nama_paket ?? $pelanggan->id_paket }}</td>
                            <td class="p-3 text-center">
                                @if(strtolower($pelanggan->status_pelanggan) == 'aktif')
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">AKTIF</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-700">TIDAK AKTIF</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex justify-center gap-4">
                                    <a href="{{ route('admin.pelanggan.edit', $pelanggan) }}"
                                        class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Tombol Hapus Trigger Modal --}}
                                    <button @click="showModal = true; pelangganId = '{{ $pelanggan->id_pelanggan }}'"
                                        class="text-red-500 hover:text-red-700" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-10">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $pelanggans->appends(request()->query())->links() }}</div>
    </main>

    {{-- Modal Hapus --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus pelanggan ini? Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex justify-end gap-2">
                <button @click="showModal = false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Batal</button>
                <form :action="'/admin/pelanggan/' + pelangganId" method="POST" class="inline">
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
