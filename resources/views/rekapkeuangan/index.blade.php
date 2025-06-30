<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rekap Keuangan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" xintegrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            Rekap Keuangan
        </h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-user-circle text-2xl md:text-3xl text-blue-600"></i>
            <span class="text-gray-700 text-sm md:text-base">Admin</span>
        </div>
    </header>

    <!-- Filter Periode -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <form action="{{route('admin.rekapkeuangan.index')}}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="w-full sm:w-auto">
                <label for="bulan" class="sr-only">Bulan</label>
                <select name="bulan" id="bulan" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="">-- Semua Bulan --</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="tahun" class="sr-only">Tahun</label>
                <select name="tahun" id="tahun" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="">-- Semua Tahun --</option>
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-blue-700">
                Tampilkan Rekap
            </button>
        </form>
    </div>

    <div class="space-y-8">
        <!-- Rekap Tagihan Lunas -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <h2 class="text-xl font-semibold p-4 border-b">Jumlah Tagihan Lunas</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-left">
                        <tr>
                            <th class="p-3 font-semibold uppercase">Periode</th>
                            <th class="p-3 font-semibold uppercase text-right">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($rekapLunas ?? [] as $rekap)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $rekap->periode }}</td>
                                <td class="p-3 text-right font-medium">{{ number_format($rekap->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center p-5 text-gray-500">Tidak ada data tagihan lunas untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    {{-- Total Keseluruhan Lunas --}}
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td class="p-3 text-right">TOTAL KESELURUHAN LUNAS</td>
                            <td class="p-3 text-right">Rp {{ number_format(($totalKeseluruhanLunas ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Rekap Tagihan Belum Lunas -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <h2 class="text-xl font-semibold p-4 border-b">Jumlah Tagihan Belum Lunas</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-left">
                        <tr>
                            <th class="p-3 font-semibold uppercase">Periode</th>
                            <th class="p-3 font-semibold uppercase text-right">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($rekapBelumLunas ?? [] as $rekap)
                             <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $rekap->periode }}</td>
                                <td class="p-3 text-right font-medium">{{ number_format($rekap->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center p-5 text-gray-500">Tidak ada data tagihan yang belum lunas untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                     {{-- Total Keseluruhan Belum Lunas --}}
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td class="p-3 text-right">TOTAL KESELURUHAN BELUM LUNAS</td>
                            <td class="p-3 text-right">Rp {{ number_format(($totalKeseluruhanBelumLunas ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

  </main>
</body>
</html>
