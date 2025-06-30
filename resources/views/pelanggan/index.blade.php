<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Pelanggan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style> body { background-color: #f0f4f8; } </style>
</head>
<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')
  <main class="md:ml-64 flex-1 p-6">
    <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Data Pelanggan</h1>
    </header>
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="flex justify-between items-center">
            {{-- PERBAIKAN: Nama rute disesuaikan dengan awalan 'admin.' --}}
            <a href="{{ route('admin.pelanggan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                <i class="fas fa-user-plus mr-2"></i>Tambah Pelanggan
            </a>
            <form action="{{ route('admin.pelanggan.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" placeholder="Cari..." value="{{ request('search') }}" class="px-4 py-2 border rounded-lg">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 border-l-4 border-green-500">{{ session('success') }}</div>
    @endif
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
                  <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">AKTIF</span>
                @else
                  <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-700">TIDAK AKTIF</span>
                @endif
              </td>
              <td class="p-3 text-center">
                <div class="flex justify-center gap-4">
                  {{-- PERBAIKAN: Nama rute disesuaikan dengan awalan 'admin.' --}}
                  <a href="{{ route('admin.pelanggan.edit', $pelanggan) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.pelanggan.destroy', $pelanggan) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center p-10">Data tidak ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-6">{{ $pelanggans->appends(request()->query())->links() }}</div>
  </main>
</body>
</html>
