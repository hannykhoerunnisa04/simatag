<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Pemutusan Layanan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body { background-color: #f0f4f8; }
  </style>
</head>

<body class="flex bg-gray-100 font-sans">
  {{-- Sidebar --}}
  @include('components.sidebar.admin-sidebar')

  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Manajemen Data Pemutusan
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    <!-- Tombol Aksi & Search -->
    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <a href="{{ route('admin.pemutusan.create') }}"
         class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow hover:shadow-lg transition">
        <i class="fas fa-plus-circle"></i> Tambah Data Pemutusan
      </a>
      <form action="{{ route('admin.pemutusan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
        <input type="text" name="search" placeholder="Cari ID atau pelanggan..."
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
      <div class="mb-4 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-r-lg shadow">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-r-lg shadow">{{ session('error') }}</div>
    @endif

    <!-- Table -->
    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
      <table class="min-w-full table-auto text-sm">
        <thead class="bg-blue-600 text-white text-left rounded-t-lg">
          <tr>
            <th class="p-3 font-semibold uppercase">ID Pemutusan</th>
            <th class="p-3 font-semibold uppercase">Pelanggan</th>
            <th class="p-3 font-semibold uppercase">Tanggal Pemutusan</th>
            <th class="p-3 font-semibold uppercase">Alasan</th>
            <th class="p-3 font-semibold uppercase text-center">Status</th>
            <th class="p-3 font-semibold uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($pemutusans ?? [] as $item)
            <tr class="hover:bg-blue-50 transition-colors duration-150">
              <td class="p-3 font-mono text-xs">{{ $item->id_pemutusan }}</td>
              <td class="p-3">{{ $item->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
              <td class="p-3">{{ \Carbon\Carbon::parse($item->tgl_pemutusan)->format('d M Y') }}</td>
              <td class="p-3 max-w-xs truncate" title="{{ $item->alasan_pemutusan }}">{{ $item->alasan_pemutusan }}</td>
              <td class="p-3 text-center">
                @if(strtolower($item->status_pemutusan) == 'permanen')
                  <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 uppercase">{{ $item->status_pemutusan }}</span>
                @elseif(strtolower($item->status_pemutusan) == 'sementara')
                  <span class="px-3 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-700 uppercase">{{ $item->status_pemutusan }}</span>
                @else
                  <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">{{ $item->status_pemutusan }}</span>
                @endif
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-3">
                  <a href="{{ route('admin.pemutusan.edit', $item) }}"
                     class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.pemutusan.destroy', $item) }}" method="POST"
                        onsubmit="return confirm('Hapus data pemutusan {{ $item->id_pemutusan }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center p-10 text-gray-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                <p class="font-semibold">Belum ada data pemutusan.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      @if(isset($pemutusans) && $pemutusans->hasPages())
        {{ $pemutusans->links('vendor.pagination.tailwind') }}
      @endif
    </div>
  </main>
</body>
</html>
