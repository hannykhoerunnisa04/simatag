<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Data Pemutusan</title>
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
            Tambah Data Pemutusan
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    {{-- Form Tambah Pemutusan --}}
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
      @if (session('error'))
          <div class="mb-6 p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-r-lg" role="alert">
              {{ session('error') }}
          </div>
      @endif


      <form action="{{ route('admin.pemutusan.store') }}" method="POST">
        @csrf
        <div class="space-y-6 max-w-lg mx-auto">
            
            <div>
              <label for="id_pemutusan" class="block mb-2 text-sm font-medium text-gray-900">ID Pemutusan</label>
              <input type="text" id="id_pemutusan" name="id_pemutusan" value="{{ old('id_pemutusan') }}" class="bg-gray-50 border @error('id_pemutusan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Masukkan ID unik untuk pemutusan" required>
              @error('id_pemutusan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="id_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">Pilih Pelanggan</label>
              <select id="id_pelanggan" name="id_pelanggan" class="bg-gray-50 border @error('id_pelanggan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                <option value="" disabled selected>-- Pilih Pelanggan Aktif --</option>
                {{-- Loop data pelanggan dari controller --}}
                @foreach($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->id_pelanggan }}" {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                        {{ $pelanggan->nama_pelanggan }} (ID: {{ $pelanggan->id_pelanggan }})
                    </option>
                @endforeach
              </select>
              @error('id_pelanggan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
            
            <div>
              <label for="tgl_pemutusan" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pemutusan</label>
              <input type="date" id="tgl_pemutusan" name="tgl_pemutusan" value="{{ old('tgl_pemutusan', date('Y-m-d')) }}" class="bg-gray-50 border @error('tgl_pemutusan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
              @error('tgl_pemutusan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
            
            <div>
              <label for="status_pemutusan" class="block mb-2 text-sm font-medium text-gray-900">Status Pemutusan</label>
              <select id="status_pemutusan" name="status_pemutusan" class="bg-gray-50 border @error('status_pemutusan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                <option value="sementara" {{ old('status_pemutusan') == 'sementara' ? 'selected' : '' }}>Sementara</option>
                <option value="permanen" {{ old('status_pemutusan') == 'permanen' ? 'selected' : '' }}>Permanen</option>
                <option value="selesai" {{ old('status_pemutusan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
              </select>
              @error('status_pemutusan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
                <label for="alasan_pemutusan" class="block mb-2 text-sm font-medium text-gray-900">Alasan Pemutusan (Opsional)</label>
                <textarea id="alasan_pemutusan" name="alasan_pemutusan" rows="4" class="bg-gray-50 border @error('alasan_pemutusan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Pelanggan menunggak pembayaran selama 3 bulan.">{{ old('alasan_pemutusan') }}</textarea>
                @error('alasan_pemutusan')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end gap-4">
          <a href="{{ route('admin.pemutusan.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700">
            Batal
          </a>
          <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Simpan Data
          </button>
        </div>
      </form>
    </div>

  </main>
</body>
</html>
