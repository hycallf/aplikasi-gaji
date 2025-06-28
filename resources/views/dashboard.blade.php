<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Grid untuk semua komponen dashboard --}}

            <div class="space-y-6">
                @if ($showUrgentMessage)
                    <div x-data="{ show: false }" x-init="show = new Date().getHours() >= 15" x-show="show" x-transition
                        class="lg:col-span-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md shadow-md"
                        role="alert">
                        <div class="flex">
                            <div class="py-1"><svg class="fill-current h-6 w-6 text-yellow-500 mr-4"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5.048V7.02a1 1 0 012 0v5.932a1 1 0 01-2 0zM9 14a1 1 0 112 0 1 1 0 01-2 0z" />
                                </svg></div>
                            <div>
                                <p class="font-bold">Pengingat Penting!</p>
                                <p class="text-sm">Data absensi untuk hari ini,
                                    <strong>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</strong>, belum
                                    terisi lengkap.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('attendances.index', ['date' => now()->toDateString()]) }}"
                                        class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 text-xs rounded-lg shadow-lg">Buka
                                        Halaman Absensi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-gray-500 text-sm font-medium">Karyawan Aktif</h3>
                        <p class="text-3xl font-bold mt-1">{{ $totalKaryawanAktif }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-gray-500 text-sm font-medium">Total User Akun</h3>
                        <p class="text-3xl font-bold mt-1">{{ $totalUser }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-gray-500 text-sm font-medium">Gaji Dibayarkan (Bulan Ini)</h3>
                        <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm lg:col-span-1">
                        <h3 class="text-lg font-semibold mb-4">Rekap Kehadiran (Bulan Ini)</h3>
                        <div class="h-full flex items-center justify-center" style="max-height: 350px;">
                            <canvas id="kehadiranChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm lg:col-span-2">
                        <div class="flex flex-wrap justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Kalender Karyawan</h3>
                            <select id="employee-select-calendar" class="border-gray-300 rounded-md shadow-sm text-sm"
                                style="width: 200px;">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="calendar"></div>
                        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-xs">
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-1.5"
                                    style="background-color: #22c55e;"></span>Hadir</div>
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-1.5"
                                    style="background-color: #3b82f6;"></span>Telat</div>
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-1.5"
                                    style="background-color: #eab308;"></span>Izin</div>
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-1.5"
                                    style="background-color: #f97316;"></span>Sakit</div>
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-1.5"
                                    style="background-color: #ef4444;"></span>Pulang Awal</div>
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-1.5"
                                    style="background-color: #d1d5db;"></span>Belum Diisi</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm lg:col-span-2">
                        <h3 class="text-lg font-semibold mb-4">Total Gaji (6 Bulan Terakhir)</h3>
                        <div class="h-80">
                            <canvas id="pengeluaranGajiChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm lg:col-span-1">
                        <h3 class="text-lg font-semibold mb-4">Komposisi Gaji (Bulan Ini)</h3>
                        <div class="h-80 mx-auto" style="max-width: 280px;">
                            <canvas id="komponenGajiChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil data dari PHP Controller dan ubah jadi format JSON yang aman untuk JavaScript
                const kehadiranData = @json($kehadiranBulanIni);
                const pengeluaranData = @json($pengeluaran6Bulan);
                const komponenData = @json($komponenGajiBulanIni);

                // --- 1. Konfigurasi Grafik Komponen Gaji (Donut) ---
                const ctxKomponen = document.getElementById('komponenGajiChart');
                new Chart(ctxKomponen, {
                    type: 'doughnut',
                    data: {
                        labels: ['Gaji Pokok', 'Transport', 'Lembur', 'Insentif'],
                        datasets: [{
                            label: 'Jumlah Pengeluaran',
                            data: [
                                komponenData.pokok,
                                komponenData.transport,
                                komponenData.lembur,
                                komponenData.insentif
                            ],
                            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#8b5cf6'],
                            hoverOffset: 4
                        }]
                    }
                });

                // --- 2. Konfigurasi Grafik Pengeluaran Gaji (Batang) ---
                const ctxPengeluaran = document.getElementById('pengeluaranGajiChart');
                new Chart(ctxPengeluaran, {
                    type: 'bar',
                    data: {
                        labels: pengeluaranData.labels,
                        datasets: [{
                            label: 'Total Gaji Bersih (Rp)',
                            data: pengeluaranData.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // --- 3. Konfigurasi Grafik Kehadiran (Pie) ---
                const ctxKehadiran = document.getElementById('kehadiranChart');
                new Chart(ctxKehadiran, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(kehadiranData).map(key => key.charAt(0).toUpperCase() + key.slice(
                            1)),
                        datasets: [{
                            label: 'Jumlah Hari',
                            data: Object.values(kehadiranData),
                            backgroundColor: ['#10b981', '#f97316', '#eab308', '#ef4444', '#6b7280'],
                            hoverOffset: 4
                        }]
                    }
                });

                var calendarEl = document.getElementById('calendar');
                var employeeSelect = document.getElementById('employee-select-calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },
                    height: 380,
                    hiddenDays: [0], // Sembunyikan hari Minggu (0)
                    locale: 'id',
                    firstDay: 1, // Mulai dari Senin

                    // events adalah "sumber data" kalender.
                    // Ia akan dipanggil setiap kali kalender butuh data baru (saat ganti bulan/karyawan)
                    events: function(fetchInfo, successCallback, failureCallback) {
                        const employeeId = employeeSelect.value;

                        // Jika tidak ada karyawan yang dipilih, jangan lakukan apa-apa
                        if (!employeeId) {
                            successCallback([]); // Kirim data kosong ke kalender
                            return;
                        }

                        // Ambil tanggal mulai & selesai dari tampilan kalender saat ini
                        const start = FullCalendar.formatDate(fetchInfo.start, {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        });
                        const end = FullCalendar.formatDate(fetchInfo.end, {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        });

                        // Bangun URL dengan parameter yang benar
                        const url =
                            `{{ route('dashboard.employee_calendar') }}?employee_id=${employeeId}&start=${start}&end=${end}`;

                        // Ambil data dari server
                        fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                successCallback(
                                data); // Kirim data yang diterima ke kalender untuk digambar
                            })
                            .catch(error => {
                                console.error('Error fetching calendar data:', error);
                                alert('Gagal memuat data kalender.');
                                failureCallback(error);
                            });
                    }
                });

                // Render kalender saat halaman pertama kali dimuat
                calendar.render();

                // Tambahkan event listener ke dropdown karyawan
                employeeSelect.addEventListener('change', function() {
                    // Saat pilihan berubah, perintahkan FullCalendar untuk mengambil ulang data event
                    calendar.refetchEvents();
                });
            });
        </script>
    @endpush
</x-app-layout>
