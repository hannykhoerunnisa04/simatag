<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm bg-[#f7fafd] border-2 border-blue-500 p-6 rounded-lg shadow-md">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logo-wireka2.png') }}" alt="Logo Aplikasi" class="w-36 h-36">
        </div>

        <!-- Judul -->
        <h2 class="text-2xl font-bold text-center text-[#1f3b8f] mb-6">Login</h2>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <input id="email" name="email" type="email"
                    class="w-full px-4 py-3 border-2 border-blue-400 rounded-md text-center focus:outline-none focus:border-blue-600"
                    placeholder="Email Address" value="{{ old('email') }}" required autofocus />
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <input id="password" name="password" type="password"
                    class="w-full px-4 py-3 border-2 border-blue-400 rounded-md text-center focus:outline-none focus:border-blue-600"
                    placeholder="Password" required />
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Login -->
            <div class="mt-6">
                <button type="submit"
                    class="w-full py-3 rounded-md bg-blue-600 text-white font-semibold hover:bg-blue-700 transition duration-200">
                    Login
                </button>
            </div>
        </form>
    </div>

</body>
</html>
