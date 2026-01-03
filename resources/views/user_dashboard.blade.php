<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                {{-- ERROR HANDLING --}}
                @if (isset($error))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p>{{ $error }}</p>
                    </div>
                @endif

                @if (isset($latestPayroll))
                    {{-- WELCOME MESSAGE --}}
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-2xl font-bold mb-2">
                            Selamat Datang, {{ $employee->nama_lengkap ?? $employee->nama }}!
                        </h3>
                        <p class="text-blue-100">
                            @if($isDosen)
                                <i class="fa-solid fa-graduation-cap mr-2"></i>
                                Dosen {{ $employee->status_dosen ?? 'Aktif' }}
                                @if($employee->nidn)
                                    | NIDN: {{ $employee->nidn }}
                                @endif
                            @else
                                <i class="fa-solid fa-briefcase mr-2"></i>
                                {{ $employee->jabatan }}
                            @endif
                        </p>
                    </div>

                    {{-- SUMMARY CARDS GAJI --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-gray-500 text-sm font-medium">Gaji Kotor</h3>
                                <i class="fa-solid fa-wallet text-blue-500 text-xl"></i>
                            </div>
                            <p class="text-3xl font-bold">Rp {{ number_format($latestPayroll->gaji_kotor, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::create($latestPayroll->periode_tahun, $latestPayroll->periode_bulan)->isoFormat('MMMM Y') }}</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-gray-500 text-sm font-medium">Total Potongan</h3>
                                <i class="fa-solid fa-minus-circle text-red-500 text-xl"></i>
                            </div>
                            <p class="text-3xl font-bold text-red-500">- Rp {{ number_format($latestPayroll->total_potongan, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::create($latestPayroll->periode_tahun, $latestPayroll->periode_bulan)->isoFormat('MMMM Y') }}</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-2 border-green-500">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-gray-500 text-sm font-medium">Gaji Diterima</h3>
                                <i class="fa-solid fa-check-circle text-green-500 text-xl"></i>
                            </div>
                            <p class="text-3xl font-bold text-green-600">Rp {{ number_format($latestPayroll->gaji_bersih, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::create($latestPayroll->periode_tahun, $latestPayroll->periode_bulan)->isoFormat('MMMM Y') }}</p>
                        </div>
                    </div>

                    @if($isDosen)
                        {{-- DASHBOARD DOSEN --}}

                        {{-- Info Tahun Ajaran & Pertemuan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($activeAcademicYear)
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                                        <i class="fa-solid fa-graduation-cap mr-2 text-blue-600"></i>
                                        Tahun Ajaran Aktif
                                    </h3>
                                    <div class="bg-blue-50 p-4 rounded">
                                        <p class="font-bold text-blue-800 text-xl">{{ $activeAcademicYear->nama_lengkap }}</p>
                                        <p class="text-sm text-gray-600 mt-2">
                                            {{ $activeAcademicYear->tanggal_mulai->format('d M Y') }} -
                                            {{ $activeAcademicYear->tanggal_selesai->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-600">Total Mata Kuliah Diampu:</p>
                                        <p class="text-3xl font-bold text-blue-600">{{ $enrollments->count() }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold mb-4 flex items-center">
                                    <i class="fa-solid fa-calendar-check mr-2 text-purple-600"></i>
                                    Pertemuan Bulan Ini
                                </h3>
                                <div class="text-center py-4">
                                    <p class="text-5xl font-bold text-purple-600">{{ $pertemuanBulanIni }}</p>
                                    <p class="text-sm text-gray-500 mt-2">Total Pertemuan</p>
                                </div>
                                <div class="mt-4 pt-4 border-t">
                                    <p class="text-xs text-gray-500 mb-2">Periode:</p>
                                    <p class="font-semibold">{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Daftar Mata Kuliah --}}
                        @if($enrollments->isNotEmpty())
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold mb-4 flex items-center">
                                    <i class="fa-solid fa-book mr-2 text-indigo-600"></i>
                                    Mata Kuliah yang Diampu
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($enrollments as $enrollment)
                                        @php
                                            $pertemuan = $pertemuanPerMatkul->where('enrollment_id', $enrollment->id)->first();
                                            $jumlahPertemuan = $pertemuan ? $pertemuan->jumlah_pertemuan : 0;
                                        @endphp
                                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-800">{{ $enrollment->matkul->nama_matkul }}</h4>
                                                    <p class="text-sm text-gray-500">{{ $enrollment->matkul->sks }} SKS</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold text-purple-600">{{ $jumlahPertemuan }}</p>
                                                    <p class="text-xs text-gray-500">Pertemuan</p>
                                                </div>
                                            </div>
                                            @if($enrollment->kelas)
                                                <div class="mt-2 flex items-center text-xs text-gray-600">
                                                    <i class="fa-solid fa-users mr-1"></i>
                                                    Kelas {{ $enrollment->kelas }}
                                                    @if($enrollment->jumlah_mahasiswa)
                                                        ({{ $enrollment->jumlah_mahasiswa }} mahasiswa)
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Tren Pertemuan --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fa-solid fa-chart-line mr-2 text-green-600"></i>
                                Tren Pertemuan (6 Bulan Terakhir)
                            </h3>
                            <div class="h-80">
                                <canvas id="trenPertemuanChart"></canvas>
                            </div>
                        </div>

                    @else
                        {{-- DASHBOARD KARYAWAN --}}

                        {{-- Ringkasan Kehadiran Bulan Ini --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-gray-500 text-sm font-medium">Hadir</h3>
                                    <i class="fa-solid fa-check-circle text-green-500 text-2xl"></i>
                                </div>
                                <p class="text-4xl font-bold text-green-600">{{ $kehadiranBulanIni->get('hadir', 0) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hari</p>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-gray-500 text-sm font-medium">Izin</h3>
                                    <i class="fa-solid fa-file-lines text-yellow-500 text-2xl"></i>
                                </div>
                                <p class="text-4xl font-bold text-yellow-600">{{ $kehadiranBulanIni->get('izin', 0) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hari</p>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-orange-500">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-gray-500 text-sm font-medium">Sakit</h3>
                                    <i class="fa-solid fa-head-side-cough text-orange-500 text-2xl"></i>
                                </div>
                                <p class="text-4xl font-bold text-orange-600">{{ $kehadiranBulanIni->get('sakit', 0) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Hari</p>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-gray-500 text-sm font-medium">Persentase Kehadiran</h3>
                                    <i class="fa-solid fa-percent text-blue-500 text-2xl"></i>
                                </div>
                                <p class="text-4xl font-bold text-blue-600">{{ number_format($persentaseKehadiran, 1) }}%</p>
                                <p class="text-xs text-gray-500 mt-1">Dari {{ $totalWorkDays }} hari kerja</p>
                            </div>
                        </div>

                        {{-- Tren Kehadiran --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fa-solid fa-chart-line mr-2 text-green-600"></i>
                                Tren Kehadiran (6 Bulan Terakhir)
                            </h3>
                            <div class="h-80">
                                <canvas id="trenKehadiranChart"></canvas>
                            </div>
                        </div>
                    @endif

                    {{-- Tren Gaji (Untuk Semua) --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fa-solid fa-money-bill-trend-up mr-2 text-blue-600"></i>
                            Tren Gaji Diterima (6 Bulan Terakhir)
                        </h3>
                        <div class="h-80">
                            <canvas id="trenGajiChart"></canvas>
                        </div>
                    </div>

                @else
                    {{-- Tampilan jika belum ada data gaji --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-900 dark:text-gray-100">
                            <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-semibold">Selamat Datang!</h3>
                            <p class="mt-2 text-gray-600">Belum ada riwayat penggajian yang bisa ditampilkan saat ini.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        @if (isset($latestPayroll))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const gajiData = @json($gaji6Bulan);

                    // Grafik Tren Gaji
                    const ctxGaji = document.getElementById('trenGajiChart');
                    new Chart(ctxGaji, {
                        type: 'line',
                        data: {
                            labels: gajiData.labels,
                            datasets: [{
                                label: 'Gaji Diterima',
                                data: gajiData.data,
                                fill: true,
                                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                                borderColor: 'rgb(22, 163, 74)',
                                tension: 0.4,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: context => 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y)
                                    }
                                }
                            }
                        }
                    });

                    @if($isDosen)
                        // Grafik Tren Pertemuan Dosen
                        const trenPertemuanData = @json($trenPertemuan);
                        const ctxPertemuan = document.getElementById('trenPertemuanChart');
                        new Chart(ctxPertemuan, {
                            type: 'bar',
                            data: {
                                labels: trenPertemuanData.labels,
                                datasets: [{
                                    label: 'Jumlah Pertemuan',
                                    data: trenPertemuanData.data,
                                    backgroundColor: 'rgba(147, 51, 234, 0.5)',
                                    borderColor: 'rgba(147, 51, 234, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    @else
                        // Grafik Tren Kehadiran Karyawan
                        const trenKehadiranData = @json($trenKehadiran);
                        const ctxKehadiran = document.getElementById('trenKehadiranChart');
                        new Chart(ctxKehadiran, {
                            type: 'line',
                            data: {
                                labels: trenKehadiranData.labels,
                                datasets: [{
                                    label: 'Hari Hadir',
                                    data: trenKehadiranData.data,
                                    fill: true,
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    borderColor: 'rgb(34, 197, 94)',
                                    tension: 0.4,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    @endif
                });
            </script>
        @endif
    @endpush
</x-app-layout>
