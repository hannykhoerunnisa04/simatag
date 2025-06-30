<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Tagihan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body { background-color: #f0f4f8; }
  </style>
</head>

<body class="flex bg-gray-100 font-sans">
  {{-- Memanggil komponen sidebar --}}
  @include('components.sidebar.admin-sidebar')

  <!-- Konten Utama -->
  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Data Tagihan - <span class="text-blue-600">SIMA-TAG</span>
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    <!-- Tombol Aksi dan Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <a href="{{ route('admin.tagihan.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
        <i class="fas fa-plus-circle"></i> Tambah Tagihan
      </a>
      <form action="{{ route('admin.tagihan.index') }}" method="GET" class="flex items-center gap-2">
        <input type="text" name="search" placeholder="Cari ID atau nama pelanggan..." value="{{ request('search') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow"><i class="fas fa-search"></i></button>
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
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">ID Tagihan</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">Nama Pelanggan</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">Periode</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">Jatuh Tempo</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider text-right">Jumlah (Rp)</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider text-center">Status</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($tagihans as $tagihan)
            <tr class="hover:bg-blue-50 transition-colors duration-150">
              <td class="px-5 py-4 whitespace-nowrap font-mono text-xs">{{ $tagihan->id_tagihan }}</td>
              {{-- Mengambil nama pelanggan melalui relasi --}}
              <td class="px-5 py-4 whitespace-nowrap">{{ $tagihan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
              <td class="px-5 py-4 whitespace-nowrap">{{ $tagihan->periode }}</td>
              <td class="px-5 py-4 whitespace-nowrap">{{ $tagihan->tgl_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d M Y') : '-' }}</td>
              <td class="px-5 py-4 whitespace-nowrap text-right font-medium">{{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
              <td class="px-5 py-4 whitespace-nowrap text-center">
                @if(strtolower($tagihan->status_tagihan) == 'lunas')
                  <span class="px-3 py-1 text-xs font-bold leading-tight rounded-full bg-green-100 text-green-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                @elseif(strtolower($tagihan->status_tagihan) == 'telat')
                  <span class="px-3 py-1 text-xs font-bold leading-tight rounded-full bg-orange-100 text-orange-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                @else
                  <span class="px-3 py-1 text-xs font-bold leading-tight rounded-full bg-red-100 text-red-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                @endif
              </td>
              <td class="px-5 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-4">
                  <a href="{{ route('admin.tagihan.edit', $tagihan) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.tagihan.destroy', $tagihan) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus tagihan ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center px-5 py-10 text-gray-500">
                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                <p class="font-semibold">Belum ada data tagihan.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- <div class="mt-6">
      {{ $tagihans->links('vendor.pagination.tailwind') }}
    </div> --}}
  </main>

</body>
</html>
