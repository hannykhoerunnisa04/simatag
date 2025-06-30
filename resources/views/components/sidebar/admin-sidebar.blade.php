<!-- resources/views/components/sidebar/admin-sidebar.blade.php -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>

<div id="main-sidebar" class="fixed inset-y-0 left-0 w-64 bg-blue-600 text-white z-50 overflow-y-auto">
  <div class="flex items-center justify-center py-6 bg-white border-b border-blue-400">
    <a href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('images/logo-wireka2.png') }}" alt="Logo Wireka" class="w-28 h-auto object-contain">
    </a>
  </div>
  <nav class="p-4 space-y-2 text-sm font-semibold">
    {{-- Menu Beranda --}}
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-home mr-3 w-5 text-center"></i> Beranda
    </a>

    {{-- Menu Data Pengguna --}}
    <a href="{{ route('admin.pengguna.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.pengguna.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-users mr-3 w-5 text-center"></i> Data Pengguna
    </a>
    
    {{-- Menu Data Pelanggan --}}
    <a href="{{ route('admin.pelanggan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.pelanggan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-address-book mr-3 w-5 text-center"></i> Data Pelanggan
    </a>

    {{-- Menu Tagihan --}}
    <a href="{{ route('admin.tagihan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.tagihan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center"></i> Tagihan
    </a>
    
    {{-- Menu Validasi Bukti --}}
    <a href="{{ route('admin.validasibukti.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.validasibukti.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center"></i> Validasi Bukti
    </a>

    {{-- Menu Pemutusan --}}
    <a href="{{ route('admin.pemutusan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.pemutusan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-exclamation-triangle mr-3 w-5 text-center"></i> Pemutusan
    </a>

    {{-- Menu Paket Layanan --}}
    <a href="{{ route('admin.paketlayanan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.paketlayanan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-layer-group mr-3 w-5 text-center"></i> Paket Layanan
    </a>

    {{-- Menu Rekap Keuangan --}}
     <a href="{{ route('admin.rekapkeuangan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('admin.rekapkeuangan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-file-invoice mr-3 w-5 text-center"></i> Rekap Keuangan
    </a>

    {{-- Menu Logout --}}
    <a href="{{ route('logout') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150 hover:bg-blue-700 hover:shadow-md"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i> Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
      @csrf
    </form>
  </nav>
</div>
