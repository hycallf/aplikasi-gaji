<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (isset($latestPayroll))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Gaji Diterima
                                ({{ \Carbon\Carbon::create($latestPayroll->periode_tahun, $latestPayroll->periode_bulan)->isoFormat('MMMM YYYY') }})
                            </h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">Rp
                                {{ number_format($latestPayroll->gaji_bersih, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Potongan
                                ({{ \Carbon\Carbon::create($latestPayroll->periode_tahun, $latestPayroll->periode_bulan)->isoFormat('MMMM YYYY') }})
                            </h3>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">Rp
                                {{ number_format($latestPayroll->total_potongan, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Komposisi Gaji
                                Terakhir</h3>
                            <canvas id="komponenGajiChart"></canvas>
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Tren Gaji Bersih (6
                            Bulan Terakhir)</h3>
                        <canvas id="trenGajiChart"></canvas>
                    </div>

                </div>
            @else
                {{-- Tampilan jika belum ada data gaji --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        Belum ada data penggajian untuk ditampilkan.
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        @if (isset($latestPayroll))
            <script>
                // Ambil data dari PHP Controller
                const komponenData = @json($latestPayroll);
                const trenData = @json($gaji6Bulan);

                // --- 1. Konfigurasi Grafik Komponen Gaji (Donut) ---
                const ctxKomponen = document.getElementById('komponenGajiChart').getContext('2d');
                new Chart(ctxKomponen, {
                    type: 'doughnut',
                    data: {
                        labels: ['Gaji Pokok', 'Transport', 'Lembur', 'Insentif'],
                        datasets: [{
                            label: 'Jumlah',
                            data: [
                                komponenData.gaji_pokok,
                                komponenData.total_tunjangan_transport,
                                komponenData.total_upah_lembur,
                                komponenData.total_insentif
                            ],
                            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#8b5cf6'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });

                // --- 2. Konfigurasi Grafik Tren Gaji (Garis) ---
                const ctxTren = document.getElementById('trenGajiChart').getContext('2d');
                new Chart(ctxTren, {
                    type: 'line',
                    data: {
                        labels: trenData.labels,
                        datasets: [{
                            label: 'Gaji Bersih (Rp)',
                            data: trenData.data,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        @endif
    @endpush
</x-app-layout>
