<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Atasan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    {{-- Memuat library Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f0f4f8; }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="flex bg-gray-100 font-sans">
    @include('components.sidebar.atasan-sidebar')

    <!-- Konten Utama -->
    <main class="md:ml-64 flex-1 p-6 md:p-8 lg:p-10 w-full">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Grafik Rekapitulasi Keuangan</h1>
            <div class="flex items-center gap-3 mt-4 sm:mt-0">
                <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                <span class="text-gray-700 text-sm font-semibold">{{ Auth::user()->nama ?? 'Atasan' }}</span>
            </div>
        </header>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card bg-blue-500 text-white p-6 rounded-lg shadow-lg">
                <p class="font-semibold text-sm">TOTAL PELANGGAN AKTIF</p>
                <p class="text-3xl font-bold mt-2">{{ $totalPelangganAktif ?? 0 }}</p>
            </div>
            <div class="stat-card bg-green-500 text-white p-6 rounded-lg shadow-lg">
                <p class="font-semibold text-sm">TOTAL TAGIHAN BULAN INI</p>
                <p class="text-3xl font-bold mt-2">{{ $totalTagihanBulanIni ?? 0 }}</p>
            </div>
            <div class="stat-card bg-purple-500 text-white p-6 rounded-lg shadow-lg">
                <p class="font-semibold text-sm">TOTAL PEMUTUSAN LAYANAN</p>
                <p class="text-3xl font-bold mt-2">{{ $totalPemutusan ?? 0 }}</p>
            </div>
        </div>
        
        <!-- Konten Bawah (Grafik dan Notifikasi) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Grafik -->
            <div class="lg:col-span-2 space-y-6">
                {{-- Grafik Tagihan --}}
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold">Grafik Tagihan Tahun {{ $tahunTagihan }}</h2>
                        <form method="GET" action="{{ route('atasan.dashboard') }}">
                            <select name="tahun_tagihan" onchange="this.form.submit()" class="border rounded p-1 text-sm">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $tahunTagihan == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                    <canvas id="grafikTagihanBulanan"></canvas>
                </div>

                {{-- Grafik Pemasukan --}}
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold">Grafik Pemasukan Tahun {{ $tahunPemasukan }}</h2>
                        <form method="GET" action="{{ route('atasan.dashboard') }}">
                            <select name="tahun_pemasukan" onchange="this.form.submit()" class="border rounded p-1 text-sm">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $tahunPemasukan == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                    <canvas id="grafikPemasukanBulanan"></canvas>
                </div>
            </div>

            <!-- Kartu Pemberitahuan -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-3">Pemberitahuan Terbaru</h2>
                <ul class="space-y-3">
                    @forelse ($notifikasi as $notif)
                        <li class="text-sm text-gray-700 border-b pb-2">
                            <a href="{{ $notif['url'] ?? '#' }}" class="flex items-start hover:bg-gray-50 p-2 rounded-md">
                                <i class="fas {{ $notif['icon'] }} mr-3 mt-1"></i>
                                <div>
                                    {!! $notif['pesan'] !!}
                                    <span class="text-xs text-gray-500 block mt-1">{{ \Carbon\Carbon::parse($notif['tanggal'])->diffForHumans() }}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 p-2">Tidak ada pemberitahuan baru.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataBulanan = @json($chartDataBulanan ?? []);
            const dataPemasukan = @json($chartDataPemasukan ?? []);
            const labels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

            const mapDataToMonths = (sourceData, key) => {
                return labels.map((label, index) => {
                    const monthData = sourceData.find(d => d.bulan === label);
                    return monthData ? monthData[key] : 0;
                });
            };

            const lunasData = mapDataToMonths(dataBulanan, 'lunas');
            const belumLunasData = mapDataToMonths(dataBulanan, 'belum_lunas');
            const pemasukanData = mapDataToMonths(dataPemasukan, 'total');

            // Grafik Tagihan
            const ctxTagihan = document.getElementById('grafikTagihanBulanan').getContext('2d');
            new Chart(ctxTagihan, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Lunas', data: lunasData, backgroundColor: 'rgba(75, 192, 192, 0.7)' },
                        { label: 'Belum Lunas', data: belumLunasData, backgroundColor: 'rgba(255, 99, 132, 0.7)' }
                    ]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    responsive: true
                }
            });

            // Grafik Pemasukan
            const ctxPemasukan = document.getElementById('grafikPemasukanBulanan').getContext('2d');
            new Chart(ctxPemasukan, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pemasukan (Rp)',
                        data: pemasukanData,
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    responsive: true
                }
            });
        });
    </script>
</body>
</html>
