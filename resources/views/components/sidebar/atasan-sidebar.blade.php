<!-- resources/views/components/sidebar.blade.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<div id="main-sidebar" class="fixed inset-y-0 left-0 w-64 bg-blue-600 text-white z-50 overflow-y-auto">
  <div class="flex items-center justify-center py-6 bg-white border-b border-blue-400">
    <a href="{{ route('dashboard') }}">
        <img src="{{ asset('images/logo-wireka2.png') }}" alt="Logo Wireka" class="w-28 h-auto object-contain">
    </a>
  </div>
  <nav class="p-4 space-y-2 text-sm font-semibold">
    {{-- Menu Beranda --}}
    <a href="{{ route('dashboard') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('dashboard', 'atasan.dashboard') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-home mr-3 w-5 text-center"></i> Beranda
    </a>
    
    {{-- Menu Rekap Keuangan --}}
    <a href="{{ route('atasan.rekapkeuangan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('tagihan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-file-invoice mr-3 w-5 text-center"></i> Rekap Keuangan
    </a>

     {{-- Menu Data Pelanggan --}}
    <a href="{{ route('atasan.datapelanggan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('datapelanggan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-address-book mr-3 w-5 text-center"></i> Data Pelanggan
    </a> 
        
    {{-- Menu Data Tagihan --}}
    <a href="{{ route('atasan.datatagihan.index') }}"
       class="flex items-center px-3 py-2.5 rounded-md transition-colors duration-150
              {{ request()->routeIs('datatagihan.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700 hover:shadow-md' }}">
      <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center"></i> Data Tagihan
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