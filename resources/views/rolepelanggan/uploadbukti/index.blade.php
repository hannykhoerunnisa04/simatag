<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Upload Bukti Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { background-color: #f0f4f8; }
        .modal { transition: opacity 0.25s ease; }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    @include('components.sidebar.pelanggan-sidebar')

    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Riwayat Upload Bukti</h1>
        </header>

        <!-- Tombol Unggah Baru -->
        <div class="mb-6">
            <a href="{{ route('pelanggan.uploadbukti.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
                <i class="fas fa-upload"></i> Unggah Bukti Baru
            </a>
        </div>

        <!-- Notifikasi -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg shadow-sm flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg shadow-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tabel Riwayat -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-5 py-3 font-semibold uppercase">ID Pembayaran</th>
                        <th class="px-5 py-3 font-semibold uppercase">ID Tagihan</th>
                        <th class="px-5 py-3 font-semibold uppercase">File Bukti</th>
                        <th class="px-5 py-3 font-semibold uppercase">Metode Bayar</th>
                        <th class="px-5 py-3 font-semibold uppercase text-center">Status</th>
                        <th class="px-5 py-3 font-semibold uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    @forelse ($pembayarans as $pembayaran)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-mono text-xs">{{ $pembayaran->Id_pembayaran }}</td>
                            <td class="px-5 py-4">{{ $pembayaran->Id_tagihan }}</td>
                            <td class="px-5 py-4">
                                <button onclick="showPreviewModal('{{ asset('storage/' . $pembayaran->file_bukti) }}')"
                                        class="text-blue-600 hover:underline flex items-center gap-1">
                                    <i class="fas fa-file-image"></i>
                                    <span>Lihat Bukti</span>
                                </button>
                            </td>
                            <td class="px-5 py-4">{{ $pembayaran->metode_bayar }}</td>
                            <td class="px-5 py-4 text-center">
                                @if(strtolower($pembayaran->status_validasi) == 'valid')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">VALID</span>
                                @elseif(strtolower($pembayaran->status_validasi) == 'tidak valid')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 uppercase">TIDAK VALID</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 uppercase">PENDING</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    @if(strtolower($pembayaran->status_validasi) == 'pending')
                                        <a href="{{ route('pelanggan.uploadbukti.edit', $pembayaran) }}"
                                           class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('pelanggan.uploadbukti.destroy', $pembayaran) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Terkunci</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                                <p class="font-semibold">Belum ada riwayat upload bukti.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $pembayarans->links('vendor.pagination.tailwind') }}
        </div>
    </main>

    <!-- Modal Pratinjau Gambar -->
    <div id="previewModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden opacity-0">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-2xl w-full max-w-2xl transform transition-transform duration-300 scale-95">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-semibold">Pratinjau Bukti</h3>
                <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <div>
                <img id="previewImage" src="" alt="Pratinjau Bukti Pembayaran" class="w-full h-auto max-h-[70vh] object-contain rounded-md">
            </div>
        </div>
    </div>

    <script>
        const previewModal = document.getElementById('previewModal');
        const previewImage = document.getElementById('previewImage');

        function showPreviewModal(imageUrl) {
            previewImage.src = imageUrl;
            previewModal.classList.remove('hidden');
            setTimeout(() => {
                previewModal.classList.remove('opacity-0');
                previewModal.querySelector('.transform').classList.remove('scale-95');
            }, 10);
        }

        function closePreviewModal() {
            previewModal.classList.add('opacity-0');
            previewModal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                previewModal.classList.add('hidden');
            }, 250);
        }
    </script>
</body>
</html>
