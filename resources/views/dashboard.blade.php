<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                {{-- NOTIFIKASI ABSENSI KARYAWAN --}}
                @if ($showUrgentMessage)
                    <div x-data="{ show: false }" x-init="show = new Date().getHours() >= 15" x-show="show" x-transition
                        class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md shadow-md"
                        role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <svg class="fill-current h-6 w-6 text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5.048V7.02a1 1 0 012 0v5.932a1 1 0 01-2 0zM9 14a1 1 0 112 0 1 1 0 01-2 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold">Pengingat Absensi Karyawan!</p>
                                <p class="text-sm">Data absensi karyawan untuk hari ini, <strong>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</strong>, belum terisi lengkap.</p>
                                <div class="mt-3">
                                    <a href="{{ route('attendances.index', ['date' => now()->toDateString()]) }}"
                                        class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 text-xs rounded-lg shadow-lg">
                                        Buka Halaman Absensi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- NOTIFIKASI ABSENSI DOSEN --}}
                @if ($showDosenReminder)
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md shadow-md" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fa-solid fa-graduation-cap text-blue-500 text-2xl mr-4"></i>
                            </div>
                            <div>
                                <p class="font-bold">Pengingat Kehadiran Dosen!</p>
                                <p class="text-sm">Masih ada data kehadiran dosen yang belum terisi untuk bulan ini ({{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}).</p>
                                <div class="mt-3">
                                    <a href="{{ route('dosen.attendances.index') }}"
                                        class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 text-xs rounded-lg shadow-lg">
                                        Input Kehadiran Dosen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SUMMARY CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Karyawan Aktif</h3>
                                <p class="text-3xl font-bold mt-1">{{ $totalKaryawanAktif }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fa-solid fa-users text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Dosen Aktif</h3>
                                <p class="text-3xl font-bold mt-1">{{ $totalDosenAktif }}</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fa-solid fa-graduation-cap text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Total User Akun</h3>
                                <p class="text-3xl font-bold mt-1">{{ $totalUser }}</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fa-solid fa-user-circle text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Gaji Bulan Ini</h3>
                                <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-orange-100 p-3 rounded-full">
                                <i class="fa-solid fa-money-bill-wave text-orange-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BREAKDOWN GAJI & ENROLLMENT --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Breakdown Gaji Karyawan vs Dosen --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fa-solid fa-chart-pie mr-2 text-indigo-600"></i>
                            Breakdown Gaji (Bulan Ini)
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                                <span class="text-sm font-medium">Karyawan</span>
                                <span class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($gajiKaryawanBulanIni, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                                <span class="text-sm font-medium">Dosen</span>
                                <span class="text-lg font-bold text-blue-600">
                                    Rp {{ number_format($gajiDosenBulanIni, 0, ',', '.') }}
                                </span>
                            </div>
                            @if($gajiKaryawanBulanIni + $gajiDosenBulanIni > 0)
                                <div class="mt-4 pt-4 border-t">
                                    <div class="text-xs text-gray-600 mb-2">Persentase:</div>
                                    <div class="flex gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-green-500 h-full"
                                                style="width: {{ ($gajiKaryawanBulanIni / ($gajiKaryawanBulanIni + $gajiDosenBulanIni)) * 100 }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium">
                                            {{ number_format(($gajiKaryawanBulanIni / ($gajiKaryawanBulanIni + $gajiDosenBulanIni)) * 100, 1) }}%
                                        </span>
                                    </div>
                                    <div class="flex gap-2 mt-1">
                                        <div class="flex-1 bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-blue-500 h-full"
                                                style="width: {{ ($gajiDosenBulanIni / ($gajiKaryawanBulanIni + $gajiDosenBulanIni)) * 100 }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium">
                                            {{ number_format(($gajiDosenBulanIni / ($gajiKaryawanBulanIni + $gajiDosenBulanIni)) * 100, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm lg:col-span-2">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fa-solid fa-chalkboard-user mr-2 text-blue-600"></i>
                                Enrollment Mata Kuliah
                            </h3>
                            @if($activeAcademicYear)
                                <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                    {{ $activeAcademicYear->nama_lengkap }}
                                </span>
                            @else
                                <span class="text-xs font-bold px-2 py-1 bg-red-100 text-red-800 rounded">
                                    Tidak ada TA Aktif
                                </span>
                            @endif
                        </div>

                        @if($dosenEnrollments->count() > 0)
                            <div class="overflow-y-auto" style="max-height: 250px;">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Matkul</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total SKS</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($dosenEnrollments as $dosen)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $dosen->nama }}</td>
                                                <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $dosen->enrollments->count() }}</td>
                                                <td class="px-4 py-2 text-sm text-center text-gray-900">{{ $dosen->total_sks }}</td>
                                                <td class="px-4 py-2 text-right text-sm font-medium">
                                                    <div x-data="{ open: false }">
                                                        <button @click="open = true" class="text-blue-600 hover:text-blue-900 transition">
                                                            <i class="fa-solid fa-circle-info fa-lg"></i>
                                                        </button>

                                                        <div x-show="open"
                                                             style="display: none;"
                                                             class="fixed inset-0 z-50 overflow-y-auto"
                                                             aria-labelledby="modal-title"
                                                             role="dialog"
                                                             aria-modal="true">
                                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                                <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                                                                            Detail Mata Kuliah: {{ $dosen->nama }}
                                                                        </h3>
                                                                        <div class="mt-4 border-t pt-4">
                                                                            <ul class="divide-y divide-gray-200">
                                                                                @foreach($dosen->enrollments as $enrollment)
                                                                                    <li class="py-2 flex justify-between">
                                                                                        <div>
                                                                                            <p class="text-sm font-medium text-gray-900">{{ $enrollment->matkul->nama_matkul ?? '-' }}</p>
                                                                                            <p class="text-xs text-gray-500">{{ $enrollment->matkul->kode_matkul ?? '-' }} • Kelas {{ $enrollment->kelas }}</p>
                                                                                        </div>
                                                                                        <span class="text-sm font-semibold text-gray-600">{{ $enrollment->matkul->sks ?? 0 }} SKS</span>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                            <div class="mt-4 pt-4 border-t flex justify-between font-bold text-gray-900">
                                                                                <span>Total</span>
                                                                                <span>{{ $dosen->total_sks }} SKS</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                        <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                            Tutup
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 bg-gray-50 rounded">
                                <i class="fa-solid fa-folder-open text-3xl mb-2"></i>
                                <p>Belum ada data enrollment di tahun ajaran aktif.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- KEHADIRAN & TOP DOSEN --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Grafik Kehadiran Karyawan --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fa-solid fa-clipboard-check mr-2 text-green-600"></i>
                            Rekap Kehadiran Karyawan (Bulan Ini)
                        </h3>
                        <div class="h-72 flex items-center justify-center">
                            <canvas id="kehadiranChart"></canvas>
                        </div>
                    </div>

                    {{-- Top 5 Dosen --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fa-solid fa-trophy mr-2 text-yellow-600"></i>
                            Top 5 Dosen (Pertemuan Terbanyak)
                        </h3>
                        <div class="space-y-3">
                            @forelse($topDosen as $index => $dosen)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <p class="font-semibold">{{ $dosen->employee->nama_lengkap ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $dosen->employee->nidn ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-blue-600">{{ $dosen->total_pertemuan }}</p>
                                        <p class="text-xs text-gray-500">Pertemuan</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                                    <p>Belum ada data pertemuan bulan ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- KALENDER KARYAWAN --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex flex-wrap justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fa-solid fa-calendar-days mr-2 text-indigo-600"></i>
                            Kalender Kehadiran Karyawan
                        </h3>
                        <select id="employee-select-calendar" class="border-gray-300 rounded-md shadow-sm text-sm" style="width: 250px;">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="calendar"></div>
                    <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-xs">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-1.5" style="background-color: #22c55e;"></span>Hadir
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-1.5" style="background-color: #3b82f6;"></span>Telat
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-1.5" style="background-color: #eab308;"></span>Izin
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-1.5" style="background-color: #f97316;"></span>Sakit
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-1.5" style="background-color: #ef4444;"></span>Pulang Awal
                        </div>
                    </div>
                </div>

                {{-- GRAFIK PENGELUARAN & KOMPONEN GAJI --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm lg:col-span-2">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fa-solid fa-chart-line mr-2 text-blue-600"></i>
                            Total Gaji (6 Bulan Terakhir)
                        </h3>
                        <div class="h-80">
                            <canvas id="pengeluaranGajiChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fa-solid fa-chart-pie mr-2 text-orange-600"></i>
                            Komposisi Gaji (Bulan Ini)
                        </h3>
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

                const kehadiranData = @json($kehadiranBulanIni);
                const pengeluaranData = @json($pengeluaran6Bulan);
                const komponenData = @json($komponenGajiBulanIni);

                // Grafik Komponen Gaji
                const ctxKomponen = document.getElementById('komponenGajiChart');
                new Chart(ctxKomponen, {
                    type: 'doughnut',
                    data: {
                        labels: ['Gaji Pokok', 'Transport', 'Lembur', 'Insentif'],
                        datasets: [{
                            data: [
                                komponenData.pokok,
                                komponenData.transport,
                                komponenData.lembur,
                                komponenData.insentif
                            ],
                            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#8b5cf6'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Grafik Pengeluaran Gaji
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
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Grafik Kehadiran
                const ctxKehadiran = document.getElementById('kehadiranChart');
                new Chart(ctxKehadiran, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(kehadiranData).map(key => key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ')),
                        datasets: [{
                            data: Object.values(kehadiranData),
                            backgroundColor: ['#10b981', '#f97316', '#eab308', '#ef4444', '#3b82f6'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // FullCalendar
                var calendarEl = document.getElementById('calendar');
                var employeeSelect = document.getElementById('employee-select-calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },
                    height: 400,
                    locale: 'id',
                    firstDay: 1,
                    events: function(fetchInfo, successCallback, failureCallback) {
                        const employeeId = employeeSelect.value;
                        if (!employeeId) {
                            successCallback([]);
                            return;
                        }

                        const start = FullCalendar.formatDate(fetchInfo.start, {year: 'numeric', month: '2-digit', day: '2-digit'});
                        const end = FullCalendar.formatDate(fetchInfo.end, {year: 'numeric', month: '2-digit', day: '2-digit'});
                        const url = `{{ route('dashboard.employee_calendar') }}?employee_id=${employeeId}&start=${start}&end=${end}`;

                        fetch(url)
                            .then(response => response.json())
                            .then(data => successCallback(data))
                            .catch(error => {
                                console.error('Error:', error);
                                failureCallback(error);
                            });
                    }
                });

                calendar.render();
                employeeSelect.addEventListener('change', () => calendar.refetchEvents());
            });


        </script>
    @endpush
</x-app-layout>
