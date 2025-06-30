<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')

  <main class="flex-1 p-6 md:ml-64">
    <header class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Dashboard - 
        <span class="text-blue-500">Sistem Informasi Manajemen Tagihan (SIMA-TAG)</span>
      </h1>
      <div class="flex items-center gap-3">
        <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
        <span class="text-gray-700">Admin</span>
      </div>
    </header>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-sky-400 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
        <p class="font-semibold uppercase text-sm">Tagihan Bulan Ini</p>
        <p class="text-3xl font-bold text-right">{{ $tagihanBulanIni ?? 0 }}</p>
      </div>
      <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
        <p class="font-semibold uppercase text-sm">Tagihan Lunas</p>
        <p class="text-3xl font-bold text-right">{{ $tagihanLunas ?? 0 }}</p>
      </div>
      <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
        <p class="font-semibold uppercase text-sm">Tagihan Belum Lunas</p>
        <p class="text-3xl font-bold text-right">{{ $tagihanBelumLunas ?? 0 }}</p>
      </div>
      <div class="bg-indigo-500 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between">
        <p class="font-semibold uppercase text-sm">Total Pelanggan Aktif</p>
        <p class="text-3xl font-bold text-right">{{ $pelangganAktif ?? 0 }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Kolom Utama (lebih lebar) -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Tabel Data Tagihan -->
        <div class="bg-white p-4 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-3">Data Tagihan Terbaru</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-blue-600 text-white text-left">
                <tr>
                  <th class="p-2">Pelanggan</th>
                  <th class="p-2">Jatuh Tempo</th>
                  <th class="p-2 text-right">Jumlah</th>
                  <th class="p-2 text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($tagihans as $tagihan)
                  <tr class="border-b">
                    <td class="p-2 font-medium">{{ $tagihan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                    <td class="p-2">{{ $tagihan->tgl_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d M Y') : '-' }}</td>
                    <td class="p-2 text-right">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
                    <td class="p-2 text-center">
                      @if(strtolower($tagihan->status_tagihan) == 'lunas')
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                      @else
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 uppercase">{{ $tagihan->status_tagihan }}</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td class="p-4 border text-center" colspan="4">Tidak ada data tagihan.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <!-- Tabel Data Pelanggan -->
        <div class="bg-white p-4 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-3">Data Pelanggan Terbaru</h2>
          <table class="w-full text-sm">
            <thead class="bg-blue-600 text-white text-left">
              <tr>
                <th class="p-2">Pelanggan</th>
                <th class="p-2 text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($pelanggans as $pelanggan)
                <tr class="border-b">
                  <td class="p-2 font-medium">{{ $pelanggan->nama_pelanggan }}</td>
                  <td class="p-2 text-center">
                    @if(strtolower($pelanggan->status_pelanggan) == 'aktif')
                      <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">{{ $pelanggan->status_pelanggan }}</span>
                    @else
                      <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-600 uppercase">{{ $pelanggan->status_pelanggan }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td class="p-4 border text-center" colspan="2">Tidak ada data pelanggan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Kolom Samping (lebih sempit) -->
      <div class="space-y-6">
        <div class="bg-white p-4 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-3">Pemberitahuan Terbaru</h2>
          <ul class="space-y-3">
            {{-- Diperbarui: Loop notifikasi dinamis --}}
            @forelse ($notifikasi as $notif)
                <li class="text-sm text-gray-700 border-b pb-2">
                    <a href="{{ $notif['url'] }}" class="flex items-start hover:bg-gray-50 p-2 rounded-md">
                        <i class="fas {{ $notif['icon'] }} mr-3 mt-1"></i>
                        <div>
                            {!! $notif['pesan'] !!}
                            <span class="text-xs text-gray-500 block mt-1">{{ \Carbon\Carbon::parse($notif['tanggal'])->diffForHumans() }}</span>
                        </div>
                    </a>
                </li>
            @empty
                <li class="text-sm text-gray-500 p-2">Tidak ada pemberitahuan baru.</li>
            @endforelse
          </ul>
        </div>
        <div class="flex flex-col justify-center items-center space-y-4">
          <a href="{{ route('admin.pelanggan.create') }}" class="bg-blue-600 text-white px-4 h-12 rounded-lg shadow-md w-full flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors">
            <i class="fas fa-user-plus"></i> Tambah Pelanggan
          </a>
          <a href="{{ route('admin.tagihan.create') }}" class="bg-green-600 text-white px-4 h-12 rounded-lg shadow-md w-full flex items-center justify-center gap-2 hover:bg-green-700 transition-colors">
            <i class="fas fa-file-invoice-dollar"></i> Tambah Tagihan
          </a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
