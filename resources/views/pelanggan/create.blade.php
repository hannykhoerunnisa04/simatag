<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Pelanggan Baru</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style> body { background-color: #f0f4f8; } </style>
</head>

<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')

  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex justify-between items-center mb-8 pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Pelanggan Baru</h1>
    </header>

    <div class="bg-white shadow-xl rounded-lg p-8">
      @if ($errors->any())
          <div class="mb-6 p-4 bg-red-100 text-red-700 border-l-4 border-red-500">
              <p class="font-bold">Terjadi kesalahan!</p>
              <ul class="mt-1 list-disc list-inside">
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif

      <form action="{{ route('admin.pelanggan.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          {{-- Kolom Kiri --}}
          <div class="space-y-6">
            <div>
              <label for="id_pengguna" class="block mb-2 text-sm font-medium text-gray-900">Pilih Pengguna</label>
              <select id="id_pengguna" name="id_pengguna" onchange="updateFields()" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5" required>
                  <option value="" data-nama="" disabled selected>-- Pilih Pengguna --</option>
                  @foreach($calonPelanggan as $pengguna)
                      <option value="{{ $pengguna->id_pengguna }}" 
                              data-nama="{{ $pengguna->nama }}" 
                              {{ old('id_pengguna') == $pengguna->id_pengguna ? 'selected' : '' }}>
                          {{ $pengguna->nama }} (ID: {{ $pengguna->id_pengguna }})
                      </option>
                  @endforeach
              </select>
            </div>

            <div>
                <label for="id_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">ID Pelanggan</label>
                <input type="text" id="id_pelanggan" name="id_pelanggan" value="{{ old('id_pelanggan') }}" class="bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 rounded-lg p-2.5" placeholder="ID Pelanggan otomatis" readonly required>
            </div>

            <div>
                <label for="nama_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">Nama Pelanggan</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" class="bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 rounded-lg p-2.5" placeholder="Nama otomatis" readonly required>
            </div>

            <div>
                <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5" required>{{ old('alamat') }}</textarea>
            </div>
          </div>

          {{-- Kolom Kanan --}}
          <div class="space-y-6">
            <div>
              <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-900">Nomor HP</label>
              <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg p-2.5" required>
            </div>

            <div>
              <label for="id_paket" class="block mb-2 text-sm font-medium text-gray-900">Paket Layanan</label>
              <select id="id_paket" name="id_paket" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5" required>
                  <option value="" disabled selected>-- Pilih Paket --</option>
                  @foreach($paketLayanans as $paket)
                      <option value="{{ $paket->id_paket }}" {{ old('id_paket') == $paket->id_paket ? 'selected' : '' }}>
                          {{ $paket->nama_paket }}
                      </option>
                  @endforeach
              </select>
            </div>

            <div>
              <label for="status_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">Status Pelanggan</label>
              <select id="status_pelanggan" name="status_pelanggan" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5" required>
                <option value="aktif" {{ old('status_pelanggan', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak aktif" {{ old('status_pelanggan') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
              </select>
            </div>

            <div>
              <label for="pic" class="block mb-2 text-sm font-medium text-gray-900">PIC</label>
              <input type="text" id="pic" name="pic" value="{{ old('pic') }}" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg p-2.5" placeholder="Nama PIC">
            </div>

            <div>
              <label for="email_pic" class="block mb-2 text-sm font-medium text-gray-900">Email PIC</label>
              <input type="email" id="email_pic" name="email_pic" value="{{ old('email_pic') }}" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg p-2.5" placeholder="Email PIC">
            </div>
          </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
          <a href="{{ route('admin.pelanggan.index') }}" class="py-2.5 px-5 text-sm font-medium bg-white rounded-lg border hover:bg-gray-100">Batal</a>
          <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan Pelanggan</button>
        </div>
      </form>
    </div>
  </main>
  
  <script>
    function updateFields() {
      const selectElement = document.getElementById('id_pengguna');
      const namaPelangganInput = document.getElementById('nama_pelanggan');
      const idPelangganInput = document.getElementById('id_pelanggan');
      
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const nama = selectedOption.getAttribute('data-nama');
      const idPengguna = selectedOption.value;

      namaPelangganInput.value = nama || '';

      if (idPengguna) {
        const lastThreeChars = idPengguna.slice(-3);
        idPelangganInput.value = 'PLG' + lastThreeChars;
      } else {
        idPelangganInput.value = '';
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      if (document.getElementById('id_pengguna').value) {
        updateFields();
      }
    });
  </script>
</body>
</html>
