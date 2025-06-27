<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 flex items-end gap-4">
                        <form method="GET" action="{{ route('attendances.index') }}" id="date-filter-form">
                            <div>
                                <x-input-label for="date-filter" value="Pilih Tanggal" />
                                <input type="date" name="date" id="date-filter" value="{{ $selectedDate }}"
                                    class="border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                                {{-- <-- Logika simpel: submit form saat tanggal berubah --}}
                            </div>
                        </form>
                        <div>
                            <x-input-label for="search_name" value="Cari Nama Karyawan" />
                            <x-text-input type="text" name="search_name" id="search_name" placeholder="Ketik nama..."
                                {{-- Atribut HTMX untuk filter nama --}} hx-get="{{ route('attendances.search') }}"
                                hx-trigger="keyup changed delay:500ms" hx-target="#attendance-table-body"
                                hx-indicator=".htmx-indicator" hx-include="[name='date']" />
                        </div>
                        {{-- Indikator Loading --}}
                        <span class="htmx-indicator ml-2">
                            <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </div>

                    <form method="POST" action="{{ route('attendances.store') }}">
                        @csrf
                        {{-- Tanggal tetap dikirim saat menyimpan --}}
                        <input type="hidden" name="date" value="{{ $selectedDate }}">

                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="text-xs text-left text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">Nama Karyawan</th>
                                        <th class="px-4 py-3">Kehadiran</th>
                                        <th class="px-4 py-3 keterangan-header hidden">Keterangan</th>
                                    </tr>
                                </thead>
                                {{-- Diberi ID agar bisa menjadi target HTMX --}}
                                <tbody id="attendance-table-body">
                                    {{-- Load data awal dengan include partial view --}}
                                    @include('attendances._employee_rows', [
                                        'employees' => \App\Models\Employee::where('status', 'aktif')->get(),
                                        'attendances' => \App\Models\Attendance::where('date', $selectedDate)->pluck('status', 'employee_id')->all(),
                                        'descriptions' => \App\Models\Attendance::where('date', $selectedDate)->pluck('description', 'employee_id')->all(),
                                    ])
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button>
                                Simpan Absensi
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk show/hide keterangan tetap dibutuhkan --}}
    @push('scripts')
        <script>
            // Fungsi untuk menginisialisasi listener pada baris tabel
            // Fungsi untuk menampilkan/menyembunyikan kolom Keterangan
            function initAttendanceListeners() {
                const statusSelects = document.querySelectorAll('.attendance-status');
                statusSelects.forEach(select => {
                    select.removeEventListener('change', checkAllStatuses);
                    select.addEventListener('change', checkAllStatuses);
                });
                checkAllStatuses();
            }

            // Fungsi untuk mengecek semua status (tetap sama)
            function checkAllStatuses() {
                const keteranganHeader = document.querySelector('.keterangan-header');
                const statusSelects = document.querySelectorAll('.attendance-status');
                let showKeterangan = false;
                statusSelects.forEach(select => {
                    const status = select.value;
                    const row = select.closest('tr');
                    const keteranganCell = row.querySelector('.keterangan-cell');
                    if (status !== 'hadir') {
                        keteranganCell.classList.remove('hidden');
                        showKeterangan = true;
                    } else {
                        keteranganCell.classList.add('hidden');
                    }
                });
                if (showKeterangan) {
                    keteranganHeader.classList.remove('hidden');
                } else {
                    keteranganHeader.classList.add('hidden');
                }
            }

            // Inisialisasi listener saat halaman pertama kali dimuat
            initAttendanceListeners();

            // HTMX akan memicu event ini setelah berhasil menukar konten
            // Kita perlu menginisialisasi ulang listener kita pada konten yang baru
            document.body.addEventListener('htmx:afterSwap', function(event) {
                if (event.detail.target.id === 'attendance-table-body') {
                    initAttendanceListeners();
                }
            });
        </script>
    @endpush
</x-app-layout>
