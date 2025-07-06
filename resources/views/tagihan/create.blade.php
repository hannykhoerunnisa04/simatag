<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Tagihan Baru</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style> body { background-color: #f0f4f8; } </style>
</head>

<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')

  <!-- Konten Utama -->
  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Tambah Tagihan Baru
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

      <form action="{{ route('admin.tagihan.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
          
          <div class="space-y-6">
            <div>
              <label for="id_tagihan" class="block mb-2 text-sm font-medium text-gray-900">ID Tagihan</label>
              <input type="text" id="id_tagihan" name="id_tagihan" value="{{ old('id_tagihan') }}" class="bg-gray-50 border @error('id_tagihan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: TGH-001" required>
            </div>
            
            <div>
              <label for="id_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">Pilih Pelanggan</label>
              {{-- Diperbaiki: Menambahkan onchange event untuk memanggil fungsi JS --}}
              <select id="id_pelanggan" name="id_pelanggan" onchange="updateHargaTagihan()" class="bg-gray-50 border @error('id_pelanggan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                  <option value="" data-harga="0" disabled selected>-- Pilih Pelanggan --</option>
                  @foreach($pelanggans as $pelanggan)
                      {{-- Diperbaiki: Menambahkan atribut data-harga --}}
                      <option value="{{ $pelanggan->id_pelanggan }}" 
                              data-harga="{{ $pelanggan->paket->harga ?? 0 }}" 
                              {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                          {{ $pelanggan->nama_pelanggan }} (ID: {{ $pelanggan->id_pelanggan }})
                      </option>
                  @endforeach
              </select>
            </div>

            <div>
              <label class="block mb-2 text-sm font-medium text-gray-900">Periode Tagihan</label>
              <div class="flex items-center gap-4">
                <div class="w-1/2">
                  <select name="periode_bulan" class="bg-gray-50 border @error('periode_bulan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    <option value="" disabled selected>-- Pilih Bulan --</option>
                    @for ($i = 1; $i <= 12; $i++)
                      <option value="{{ $i }}" {{ old('periode_bulan') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                    @endfor
                  </select>
                </div>
                <div class="w-1/2">
                    <select name="periode_tahun" class="bg-gray-50 border @error('periode_tahun') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="" disabled selected>-- Pilih Tahun --</option>
                        @for ($i = date('Y'); $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}" {{ old('periode_tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div>
                <label for="jumlah_tagihan" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Tagihan (Rp)</label>
                {{-- Diperbaiki: Dibuat readonly dan ditambahkan ID untuk JS --}}
                <input type="number" id="jumlah_tagihan" name="jumlah_tagihan" value="{{ old('jumlah_tagihan') }}" class="bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Pilih pelanggan untuk melihat harga" required readonly>
            </div>
            <div>
              <label for="tgl_jatuh_tempo" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Jatuh Tempo</label>
              <input type="date" id="tgl_jatuh_tempo" name="tgl_jatuh_tempo" value="{{ old('tgl_jatuh_tempo') }}" class="bg-gray-50 border @error('tgl_jatuh_tempo') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div>
              <label for="status_tagihan" class="block mb-2 text-sm font-medium text-gray-900">Status Tagihan</label>
              <select id="status_tagihan" name="status_tagihan" class="bg-gray-50 border @error('status_tagihan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                <option value="belum" {{ old('status_tagihan', 'belum') == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="lunas" {{ old('status_tagihan') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="telat" {{ old('status_tagihan') == 'telat' ? 'selected' : '' }}>Telat</option>
              </select>
            </div>
          </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
          <a href="{{ route('admin.tagihan.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100">
            Batal
          </a>
          <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Simpan Tagihan
          </button>
        </div>
      </form>
    </div>
  </main>
  
  {{-- Skrip JavaScript untuk mengisi harga otomatis --}}
  <script>
    function updateHargaTagihan() {
      // Ambil elemen dropdown dan input jumlah tagihan
      const selectPelanggan = document.getElementById('id_pelanggan');
      const jumlahTagihanInput = document.getElementById('jumlah_tagihan');
      
      // Ambil option yang sedang dipilih
      const selectedOption = selectPelanggan.options[selectPelanggan.selectedIndex];
      
      // Ambil harga dari atribut data-harga
      const harga = selectedOption.getAttribute('data-harga');
      
      // Set nilai input jumlah tagihan
      jumlahTagihanInput.value = harga || '';
    }

    // Panggil fungsi sekali saat halaman dimuat untuk mengisi jika ada data lama (old)
    document.addEventListener('DOMContentLoaded', function() {
      if (document.getElementById('id_pelanggan').value) {
        updateHargaTagihan();
      }
    });
  </script>
</body>
</html>
