<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Pengguna</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body { background-color: #f0f4f8; }
  </style>
</head>

<body class="flex bg-gray-100 font-sans">
  @include('components.sidebar.admin-sidebar')

  <!-- Konten Utama -->
  <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
            Data Pengguna
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    <!-- Tombol Aksi dan Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <a href="{{ route('admin.pengguna.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
        <i class="fas fa-user-plus"></i> Tambah Pengguna
      </a>
      <form action="{{ route('admin.pengguna.index') }}" method="GET" class="flex items-center gap-2">
        <input type="text" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
            <th class="p-3 font-semibold uppercase">ID Pengguna</th>
            <th class="p-3 font-semibold uppercase">Nama</th>
            <th class="p-3 font-semibold uppercase">Email</th>
            <th class="p-3 font-semibold uppercase text-center">Role</th>
            <th class="p-3 font-semibold uppercase">Tanggal Bergabung</th>
            <th class="p-3 font-semibold uppercase text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          @forelse ($penggunas as $pengguna)
            <tr class="hover:bg-blue-50">
              <td class="p-3 font-mono text-xs">{{ $pengguna->id_pengguna }}</td>
              <td class="p-3 font-medium">{{ $pengguna->nama ?? 'N/A' }}</td>
              <td class="p-3">{{ $pengguna->email }}</td>
              <td class="p-3 text-center">
                @if(strtolower($pengguna->role) == 'admin')
                  <span class="px-2 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase">{{ $pengguna->role }}</span>
                @elseif(strtolower($pengguna->role) == 'atasan')
                  <span class="px-2 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-700 uppercase">{{ $pengguna->role }}</span>
                @else
                  <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-700 uppercase">{{ $pengguna->role }}</span>
                @endif
              </td>
              <td class="p-3">{{ optional($pengguna->created_at)->format('d F Y') }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-4">
                  <!-- Tombol Edit -->
                  <a href="{{ route('admin.pengguna.edit', $pengguna) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>

                  <!-- Tombol Reset Password -->
                  @if (Auth::id() !== $pengguna->id_pengguna)
                    <form action="{{ route('admin.pengguna.resetPassword', $pengguna) }}" method="POST" onsubmit="return confirm('Yakin ingin reset password untuk {{ $pengguna->nama }}?');">
                      @csrf
                      <button type="submit" class="text-blue-500 hover:text-blue-700" title="Reset Password">
                        <i class="fas fa-key"></i>
                      </button>
                    </form>
                  @endif

                  <!-- Tombol Hapus -->
                  @if (Auth::id() !== $pengguna->id_pengguna)
                    <form action="{{ route('admin.pengguna.destroy', $pengguna) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center p-10 text-gray-500">
                <i class="fas fa-users-slash text-4xl mb-3"></i>
                <p class="font-semibold">Belum ada data pengguna.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      @if(isset($penggunas) && $penggunas->hasPages())
        {{ $penggunas->links('vendor.pagination.tailwind') }}
      @endif
    </div>
  </main>
</body>
</html>
