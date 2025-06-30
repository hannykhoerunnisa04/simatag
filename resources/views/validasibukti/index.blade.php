<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Validasi Bukti Pembayaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body { background-color: #f0f4f8; }
    .modal { transition: opacity 0.25s ease; }
  </style>
</head>

<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')

  <!-- Konten Utama -->
  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Validasi Bukti Pembayaran
        </h1>
    </header>

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
            <th class="p-3 font-semibold uppercase">ID Pembayaran</th>
            <th class="p-3 font-semibold uppercase">ID Tagihan</th>
            <th class="p-3 font-semibold uppercase">Nama Pelanggan</th>
            <th class="p-3 font-semibold uppercase">Tanggal Bayar</th>
            <th class="p-3 font-semibold uppercase">Metode Bayar</th>
            <th class="p-3 font-semibold uppercase text-center">Status</th>
            <th class="p-3 font-semibold uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($pembayarans ?? [] as $pembayaran)
            <tr class="hover:bg-blue-50">
              <td class="p-3 font-mono text-xs">{{ $pembayaran->Id_pembayaran }}</td>
              <td class="p-3">{{ $pembayaran->Id_tagihan }}</td>
              <td class="p-3">{{ $pembayaran->tagihan->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
              <td class="p-3">{{ \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d F Y') }}</td>
              <td class="p-3">{{ $pembayaran->metode_bayar }}</td>
              <td class="p-3 text-center">
                <span class="px-2 py-1 text-xs font-bold leading-tight rounded-full bg-yellow-100 text-yellow-700 uppercase">PENDING</span>
              </td>
              <td class="p-3 text-center">
                {{-- Diperbaiki: Menggunakan asset helper untuk path gambar --}}
                <button onclick="showValidationModal('{{ $pembayaran->Id_pembayaran }}', '{{ asset('storage/' . $pembayaran->file_bukti) }}')" class="text-blue-500 hover:text-blue-700" title="Lihat & Validasi Bukti">
                  <i class="fas fa-search-plus"></i> Lihat Bukti
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center p-10 text-gray-500">
                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                <p class="font-semibold">Belum ada bukti pembayaran yang perlu divalidasi.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      @if(isset($pembayarans) && $pembayarans->hasPages())
        {{ $pembayarans->links('vendor.pagination.tailwind') }}
      @endif
    </div>
  </main>
  
  <div id="validationModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden opacity-0">
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-2xl w-full max-w-lg transform transition-transform duration-300 scale-95">
      <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-xl font-semibold">Detail Bukti Pembayaran</h3>
        <button onclick="closeValidationModal()" class="text-gray-500 hover:text-gray-800">&times;</button>
      </div>
      <div class="mb-4">
        <img id="buktiImage" src="" alt="Bukti Pembayaran" class="w-full h-auto max-h-[60vh] object-contain rounded-md">
      </div>
      <div class="flex justify-end gap-3">
        <form id="invalidateForm" method="POST" action="">
          @csrf
          @method('POST')
          <input type="hidden" name="status" value="tidak valid">
          <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Tidak Valid</button>
        </form>
        <form id="validateForm" method="POST" action="">
          @csrf
          @method('POST')
          <input type="hidden" name="status" value="valid">
          <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Valid</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const validationModal = document.getElementById('validationModal');
    const buktiImage = document.getElementById('buktiImage');
    const validateForm = document.getElementById('validateForm');
    const invalidateForm = document.getElementById('invalidateForm');

    function showValidationModal(buktiId, imageUrl) {
      buktiImage.src = imageUrl;
      
      // PERBAIKAN: Cara membuat URL yang lebih aman
      let urlTemplate = "{{ route('admin.validasibukti.validate', ['id_bukti' => 'PLACEHOLDER']) }}";
      let actionUrl = urlTemplate.replace('PLACEHOLDER', buktiId);
      
      validateForm.action = actionUrl;
      invalidateForm.action = actionUrl;

      validationModal.classList.remove('hidden');
      setTimeout(() => {
        validationModal.classList.remove('opacity-0');
        validationModal.querySelector('.transform').classList.remove('scale-95');
      }, 10);
    }

    function closeValidationModal() {
      validationModal.classList.add('opacity-0');
      validationModal.querySelector('.transform').classList.add('scale-95');
      setTimeout(() => {
        validationModal.classList.add('hidden');
      }, 250);
    }

    window.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && !validationModal.classList.contains('hidden')) {
        closeValidationModal();
      }
    });
  </script>
</body>
</html>
