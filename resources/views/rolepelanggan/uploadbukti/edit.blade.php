<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Bukti Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { background-color: #f0f4f8; }
        input[type="file"]::file-selector-button {
            @apply bg-blue-50 border border-blue-300 text-blue-700 font-semibold px-4 py-2 rounded-lg mr-4 hover:bg-blue-100 cursor-pointer transition-colors duration-150;
        }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    @include('components.sidebar.pelanggan-sidebar')

    <!-- Konten Utama -->
    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
                Edit Bukti Pembayaran
            </h1>
        </header>

        <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-r-lg" role="alert">
                    <p class="font-bold">Terjadi kesalahan!</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pelanggan.uploadbukti.update', $pembayaran) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-6 max-w-lg mx-auto">
                    <div>
                        <label for="Id_pembayaran" class="block mb-2 text-sm font-medium text-gray-900">ID Pembayaran</label>
                        <input type="text" id="Id_pembayaran" value="{{ $pembayaran->Id_pembayaran }}" class="w-full bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5" readonly>
                    </div>

                    <div>
                        <label for="Id_tagihan" class="block mb-2 text-sm font-medium text-gray-900">ID Tagihan</label>
                        <input type="text" id="Id_tagihan" value="{{ $pembayaran->Id_tagihan }}" class="w-full bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5" readonly>
                        <p class="mt-1 text-xs text-gray-500">ID Tagihan tidak dapat diubah.</p>
                    </div>

                    <div>
                        <label for="metode_bayar" class="block mb-2 text-sm font-medium text-gray-900">Ubah Metode Pembayaran</label>
                        <select name="metode_bayar" id="metode_bayar" class="w-full bg-gray-50 border @error('metode_bayar') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg p-2.5" required>
                            <option value="Transfer Bank" {{ old('metode_bayar', $pembayaran->metode_bayar) == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="Dompet Digital" {{ old('metode_bayar', $pembayaran->metode_bayar) == 'Dompet Digital' ? 'selected' : '' }}>Dompet Digital</option>
                            <option value="Lainnya" {{ old('metode_bayar', $pembayaran->metode_bayar) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('metode_bayar')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">File Bukti Saat Ini</label>
                        <a href="{{ asset('storage/' . $pembayaran->file_bukti) }}" target="_blank">
                            <img src="{{ asset('storage/' . $pembayaran->file_bukti) }}" alt="Bukti Pembayaran Saat Ini" class="max-w-xs rounded-lg border shadow">
                        </a>
                    </div>

                    <div>
                        <label for="file_bukti" class="block mb-2 text-sm font-medium text-gray-900">Unggah File Bukti Baru (Opsional)</label>
                        <input type="file" name="file_bukti" id="file_bukti" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 @error('file_bukti') border-red-500 @enderror" onchange="previewNewImage()">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file.</p>
                        @error('file_bukti')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Area Pratinjau untuk Gambar BARU --}}
                    <div id="new-image-preview-container" class="mt-4 hidden">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Pratinjau File Baru:</label>
                        <img id="new-image-preview" src="" alt="Pratinjau Bukti Baru" class="max-w-xs rounded-lg border shadow">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('pelanggan.uploadbukti.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700">
                        Batal
                    </a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Perbarui Bukti
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewNewImage() {
            const fileInput = document.getElementById('file_bukti');
            const previewContainer = document.getElementById('new-image-preview-container');
            const previewImage = document.getElementById('new-image-preview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                previewContainer.classList.add('hidden');
                previewImage.src = '';
            }
        }
    </script>
</body>
</html>
