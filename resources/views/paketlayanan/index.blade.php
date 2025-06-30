<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Paket Layanan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            Data Paket Layanan
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    <!-- Tombol Aksi dan Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <a href="{{ route('admin.paketlayanan.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
        <i class="fas fa-plus-circle"></i> Tambah Paket Baru
      </a>
      <form action="#" method="GET" class="flex items-center gap-2">
        <input type="text" name="search" placeholder="Cari nama paket..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">ID Paket</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">Nama Paket</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">Kecepatan</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider text-right">Harga (Rp)</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider">Deskripsi</th>
            <th class="px-5 py-3 font-semibold uppercase tracking-wider text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($paketLayanans ?? [] as $paket)
            <tr class="hover:bg-blue-50 transition-colors duration-150">
              <td class="px-5 py-4 whitespace-nowrap font-mono text-xs">{{ $paket->id_paket }}</td>
              <td class="px-5 py-4 whitespace-nowrap font-semibold">{{ $paket->nama_paket }}</td>
              <td class="px-5 py-4 whitespace-nowrap">{{ $paket->kecepatan }}</td>
              <td class="px-5 py-4 whitespace-nowrap text-right font-medium">{{ number_format($paket->harga, 0, ',', '.') }}</td>
              <td class="px-5 py-4 max-w-xs truncate">{{ $paket->deskripsi }}</td>
              <td class="px-5 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-4">
                  <a href="{{ route('admin.paketlayanan.edit', $paket) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit"></i></a>
                  <button onclick="showDeleteModal('{{ $paket->id_paket }}', '{{ $paket->nama_paket }}')" class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center px-5 py-10 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-3"></i>
                <p class="font-semibold">Belum ada data paket layanan.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      {{-- Pagination links akan muncul di sini --}}
      {{-- {{ ($paketLayanans ?? null) ? $paketLayanans->links() : '' }} --}}
    </div>
  </main>

  <!-- Modal Konfirmasi Hapus -->
  <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
      <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
      </div>
      <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
      <p class="text-gray-600 text-center mb-6">
        Apakah Anda yakin ingin menghapus paket layanan <strong id="paketName"></strong>?
        <br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>
      </p>
      <div class="flex gap-3 justify-center">
        <button onclick="hideDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
          Batal
        </button>
        <form id="deleteForm" method="POST" class="inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            Ya, Hapus
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    function showDeleteModal(paketId, paketName) {
      // Set nama paket di modal
      document.getElementById('paketName').textContent = paketName;
      
      // Set action URL untuk form delete
      const deleteForm = document.getElementById('deleteForm');
      deleteForm.action = `/admin/paketlayanan/${paketId}`;
      
      // Tampilkan modal
      document.getElementById('deleteConfirmationModal').classList.remove('hidden');
    }

    function hideDeleteModal() {
      // Sembunyikan modal
      document.getElementById('deleteConfirmationModal').classList.add('hidden');
    }

    // Tutup modal jika klik di luar area modal
    document.getElementById('deleteConfirmationModal').addEventListener('click', function(e) {
      if (e.target === this) {
        hideDeleteModal();
      }
    });

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        hideDeleteModal();
      }
    });
  </script>
</body>
</html>