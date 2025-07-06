<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ubah Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style> body { background-color: #f0f4f8; } </style>
</head>
<body class="flex bg-gray-100 font-sans">
    {{-- Asumsi Anda memiliki sidebar untuk pelanggan --}}
    @include('components.sidebar.pelanggan-sidebar')

    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex justify-between items-center mb-8 pb-4 border-b">
            <h1 class="text-3xl font-bold text-gray-800">Ubah Password</h1>
            <div class="flex items-center gap-3">
                <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                <span class="text-gray-700 text-sm font-semibold">{{ Auth::user()->nama ?? 'Pelanggan' }}</span>
            </div>
        </header>

        <div class="bg-white shadow-xl rounded-lg p-8 max-w-2xl mx-auto">
            @if (session('status') === 'password-updated')
                <div class="mb-6 p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-r-lg" role="alert">
                    <p class="font-bold">Password berhasil diperbarui.</p>
                </div>
            @endif

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

            <form method="post" action="{{ route('password.change') }}" class="space-y-6">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input id="password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="{{ url()->previous() }}" class="py-2 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="py-2 px-4 bg-blue-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
