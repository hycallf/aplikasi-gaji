<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                {{-- Cek jika ada data payroll untuk ditampilkan --}}
                @if ($latestPayroll)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <h3 class="text-gray-500 text-sm font-medium">Gaji Kotor (Periode Terakhir)</h3>
                            <p class="text-3xl font-bold mt-1">Rp
                                {{ number_format($latestPayroll->gaji_kotor, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <h3 class="text-gray-500 text-sm font-medium">Total Potongan (Periode Terakhir)</h3>
                            <p class="text-3xl font-bold mt-1 text-red-500">- Rp
                                {{ number_format($latestPayroll->total_potongan, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <h3 class="text-gray-500 text-sm font-medium">Gaji Diterima (Periode Terakhir)</h3>
                            <p class="text-3xl font-bold mt-1 text-green-600">Rp
                                {{ number_format($latestPayroll->gaji_bersih, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Tren Gaji Diterima (6
                            Bulan Terakhir)</h3>
                        <div class="h-80">
                            <canvas id="trenGajiChart"></canvas>
                        </div>
                    </div>
                @else
                    {{-- Tampilan jika belum ada data gaji sama sekali --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold">Selamat Datang!</h3>
                            <p class="mt-2 text-gray-600">Belum ada riwayat penggajian yang bisa ditampilkan saat ini.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sisipkan Chart.js untuk menggambar grafik --}}
    @push('scripts')
        {{-- Pastikan Chart.js sudah diload di layout utama Anda --}}
        @if ($latestPayroll)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('trenGajiChart');
                    const gajiData = @json($gaji6Bulan);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: gajiData.labels,
                            datasets: [{
                                label: 'Gaji Diterima',
                                data: gajiData.data,
                                fill: false,
                                borderColor: 'rgb(22, 163, 74)', // Warna hijau
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        // Format angka menjadi "Rp xxx.xxx"
                                        callback: function(value, index, values) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @endif
    @endpush
</x-app-layout>
