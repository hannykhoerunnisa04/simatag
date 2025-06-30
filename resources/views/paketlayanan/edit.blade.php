<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" 
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Paket Layanan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style> body { background-color: #f0f4f8; } </style>
</head>

<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')

  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Edit Paket Layanan</h1>
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

      {{-- Diperbaiki: Nama variabel diubah menjadi $paketlayanan (l kecil) --}}
      <form action="{{ route('admin.paketlayanan.update', $paketlayanan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
          <div class="space-y-6">
            <div>
              <label for="id_paket" class="block mb-2 text-sm font-medium text-gray-900">ID Paket</label>
              <input type="text" id="id_paket" value="{{ $paketlayanan->id_paket }}" class="bg-gray-200 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" readonly>
            </div>
            <div>
              <label for="nama_paket" class="block mb-2 text-sm font-medium text-gray-900">Nama Paket</label>
              <input type="text" id="nama_paket" name="nama_paket" value="{{ old('nama_paket', $paketlayanan->nama_paket) }}" class="bg-gray-50 border @error('nama_paket') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            </div>
            <div>
              <label for="kecepatan" class="block mb-2 text-sm font-medium text-gray-900">Kecepatan</label>
              <input type="text" id="kecepatan" name="kecepatan" value="{{ old('kecepatan', $paketlayanan->kecepatan) }}" class="bg-gray-50 border @error('kecepatan') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            </div>
          </div>
          <div class="space-y-6">
             <div>
              <label for="harga" class="block mb-2 text-sm font-medium text-gray-900">Harga (Rp)</label>
              <input type="number" id="harga" name="harga" value="{{ old('harga', $paketlayanan->harga) }}" class="bg-gray-50 border @error('harga') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            </div>
            <div>
                <label for="deskripsi" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" class="bg-gray-50 border @error('deskripsi') border-red-500 @else border-gray-300 @enderror text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ old('deskripsi', $paketlayanan->deskripsi) }}</textarea>
            </div>
          </div>
        </div>
        <div class="mt-8 flex justify-end gap-4">
          <a href="{{ route('admin.paketlayanan.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100">Batal</a>
          <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Perbarui Paket</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
