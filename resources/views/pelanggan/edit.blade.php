<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Data Pelanggan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            Edit Data Pelanggan
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    {{-- Form Edit Pelanggan --}}
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

      {{-- Form action menunjuk ke route update --}}
      <form action="{{ route('admin.pelanggan.update', $pelanggan) }}" method="POST">
        @csrf
        @method('PUT') {{-- Method spoofing untuk update --}}
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
          
          {{-- Kolom Kiri --}}
          <div class="space-y-6">
            <div>
              <label for="id_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">ID Pelanggan</label>
              <input type="text" id="id_pelanggan" name="id_pelanggan" value="{{ $pelanggan->id_pelanggan }}" class="bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" readonly>
            </div>

            <div>
              <label for="nama_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap</label>
              <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" class="bg-gray-50 border @error('nama_pelanggan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
              @error('nama_pelanggan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
                <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                <textarea id="alamat" name="alamat" rows="4" class="bg-gray-50 border @error('alamat') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                @error('alamat')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
          </div>

          {{-- Kolom Kanan --}}
          <div class="space-y-6">
            <div>
              <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-900">Nomor HP</label>
              <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}" class="bg-gray-50 border @error('no_hp') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
              @error('no_hp')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="id_paket" class="block mb-2 text-sm font-medium text-gray-900">Paket Layanan</label>
              <select id="id_paket" name="id_paket" class="bg-gray-50 border @error('id_paket') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                  <option value="" disabled>-- Pilih Paket Layanan --</option>
                  @foreach($paketLayanans as $paket)
                      <option value="{{ $paket->id_paket }}" {{ old('id_paket', $pelanggan->id_paket) == $paket->id_paket ? 'selected' : '' }}>
                          {{ $paket->nama_paket }}
                      </option>
                  @endforeach
              </select>
              @error('id_paket')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="status_pelanggan" class="block mb-2 text-sm font-medium text-gray-900">Status Pelanggan</label>
              <select id="status_pelanggan" name="status_pelanggan" class="bg-gray-50 border @error('status_pelanggan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                <option value="aktif" {{ old('status_pelanggan', $pelanggan->status_pelanggan) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak aktif" {{ old('status_pelanggan', $pelanggan->status_pelanggan) == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
              </select>
              @error('status_pelanggan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="pic" class="block mb-2 text-sm font-medium text-gray-900">PIC</label>
              <input type="text" id="pic" name="pic" value="{{ old('pic', $pelanggan->pic) }}" class="bg-gray-50 border @error('pic') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Nama PIC">
              @error('pic')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="email_pic" class="block mb-2 text-sm font-medium text-gray-900">Email PIC</label>
              <input type="email" id="email_pic" name="email_pic" value="{{ old('email_pic', $pelanggan->email_pic) }}" class="bg-gray-50 border @error('email_pic') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Email PIC">
              @error('email_pic')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex justify-end gap-4">
          <a href="{{ route('admin.pelanggan.index') }}" class="py-2.5 px-5 text-sm font-medium bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700">
            Batal
          </a>
          <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Perbarui Data
          </button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
