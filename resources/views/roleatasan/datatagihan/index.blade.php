<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Tagihan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style> body { background-color: #f0f4f8; } </style>
</head>

<body class="flex bg-gray-100 font-sans">
  {{-- Memanggil komponen sidebar untuk atasan --}}
  @include('components.sidebar.atasan-sidebar')

  <!-- Konten Utama -->
  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Data Tagihan
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Atasan</span>
        </div>
    </header>

    <!-- Filter -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <form action="{{ route('atasan.datatagihan.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            <input type="text" name="search" placeholder="Cari ID Tagihan/Pelanggan, Nama..." value="{{ request('search') }}" class="w-full sm:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <select name="status" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="lunas" @if(request('status') == 'lunas') selected @endif>Lunas</option>
                <option value="belum" @if(request('status') == 'belum') selected @endif>Belum Lunas</option>
                <option value="telat" @if(request('status') == 'telat') selected @endif>Telat</option>
            </select>
            <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
      <table class="min-w-full table-auto text-sm">
        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-left">
          <tr>
            <th class="p-3 font-semibold uppercase">ID Tagihan</th>
            <th class="p-3 font-semibold uppercase">Nama Pelanggan</th>
            <th class="p-3 font-semibold uppercase">Periode</th>
            <th class="p-3 font-semibold uppercase">Jatuh Tempo</th>
            <th class="p-3 font-semibold uppercase text-right">Jumlah (Rp)</th>
            <th class="p-3 font-semibold uppercase text-center">Status</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($tagihans as $tagihan)
            <tr class="hover:bg-gray-50">
              <td class="p-3 font-mono text-xs">{{ $tagihan->id_tagihan }}</td>
              <td class="p-3 font-medium">{{ $tagihan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
              <td class="p-3">{{ $tagihan->periode }}</td>
              <td class="p-3">{{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d F Y') }}</td>
              <td class="p-3 text-right font-medium">{{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
              <td class="p-3 text-center">
                @if(strtolower($tagihan->status_tagihan) == 'lunas')
                  <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-green-100 text-green-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                @elseif(strtolower($tagihan->status_tagihan) == 'telat')
                    <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-orange-100 text-orange-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                @else
                  <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-red-100 text-red-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center p-10 text-gray-500">
                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                <p class="font-semibold">Data tagihan tidak ditemukan.</p>
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
