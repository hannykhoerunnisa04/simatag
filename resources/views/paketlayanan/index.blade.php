<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Paket Layanan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    body { background-color: #f0f4f8; }
    [x-cloak] { display: none !important; }
  </style>
</head>

<body class="flex bg-gray-100 font-sans" x-data="{ showModal: false, modalUrl: '', paketName: '' }">
  {{-- Sidebar --}}
  @include('components.sidebar.admin-sidebar')

  <!-- Konten Utama -->
  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Manajemen Data Paket
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    <!-- Tombol Aksi & Search -->
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <a href="{{ route('admin.paketlayanan.create') }}"
         class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-lg transition">
        <i class="fas fa-plus-circle"></i> Tambah Paket Baru
      </a>
      <form action="{{ route('admin.paketlayanan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
        <input type="text" name="search" placeholder="Cari nama paket..."
               value="{{ request('search') }}"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
               autocomplete="off">
        <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
      <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg shadow-sm">
        <i class="fas fa-check-circle text-green-600 text-xl"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if (session('error'))
      <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg shadow-sm">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    <!-- Table Data -->
    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
      <table class="min-w-full table-auto text-sm">
        <thead class="bg-blue-600 text-white text-left rounded-t-lg">
          <tr>
            <th class="p-3 font-semibold uppercase">ID Paket</th>
            <th class="p-3 font-semibold uppercase">Nama Paket</th>
            <th class="p-3 font-semibold uppercase">Kecepatan</th>
            <th class="p-3 font-semibold uppercase text-right">Harga (Rp)</th>
            <th class="p-3 font-semibold uppercase">Deskripsi</th>
            <th class="p-3 font-semibold uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($paketLayanans as $paket)
            <tr class="hover:bg-blue-50 transition-colors duration-150">
              <td class="p-3 font-mono text-xs">{{ $paket->id_paket }}</td>
              <td class="p-3 font-semibold">{{ $paket->nama_paket }}</td>
              <td class="p-3">{{ $paket->kecepatan }}</td>
              <td class="p-3 text-right font-medium">{{ number_format($paket->harga, 0, ',', '.') }}</td>
              <td class="p-3 max-w-xs truncate">{{ $paket->deskripsi }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-3">
                  <a href="{{ route('admin.paketlayanan.edit', $paket) }}"
                     class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit"></i></a>
                  <button @click="showModal = true; modalUrl='{{ route('admin.paketlayanan.destroy', $paket) }}'; paketName='{{ $paket->nama_paket }}';"
                          class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center p-10 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-3"></i>
                <p class="font-semibold">Belum ada data paket layanan.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      {{ $paketLayanans->links() }}
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
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Hapus</h3>
      <p class="text-gray-600 mb-6 text-center">
        Apakah Anda yakin ingin menghapus paket <strong x-text="paketName"></strong>?
        <br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>
      </p>
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
