<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                {{-- Kalender Mini Navigasi --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                    @php
                        $date = $selectedDate;
                        $prevMonth = $date->copy()->subMonth();
                        $nextMonth = $date->copy()->addMonth();
                    @endphp

                    <div class="flex items-center justify-between mb-4">
                        <a href="{{ route('attendances.index', ['date' => $prevMonth->startOfMonth()->toDateString()]) }}"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h4 class="font-bold text-xl text-gray-800 dark:text-gray-200">
                            {{ $date->isoFormat('MMMM Y') }}
                        </h4>

                        @php
                            $isNextMonthInFuture = $nextMonth->copy()->startOfMonth()->isFuture();
                        @endphp
                        @if ($isNextMonthInFuture)
                            <span class="p-2 rounded-full text-gray-300 dark:text-gray-600 cursor-not-allowed">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @else
                            <a href="{{ route('attendances.index', ['date' => $nextMonth->startOfMonth()->toDateString()]) }}"
                                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-7 gap-2 text-center text-xs text-gray-500">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                            <div class="font-semibold">{{ $dayName }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7 gap-1 mt-2">
                        @php
                            $firstDay = $date->copy()->startOfMonth()->dayOfWeekIso;
                            $startBlankDays = $firstDay - 1;
                        @endphp
                        @for ($i = 0; $i < $startBlankDays; $i++)
                            <div></div>
                        @endfor
                        @for ($day = 1; $day <= $date->daysInMonth; $day++)
                            @php
                                $currentDayDate = $date->copy()->setDay($day);
                                $isToday = $currentDayDate->isToday();
                                $isSelected = $currentDayDate->isSameDay($selectedDate);
                                $isCompleted = in_array($day, $completedDates);
                                
                                // GUNAKAN WORK DAYS DARI SETTINGS
                                $isNonWorkDay = !in_array($currentDayDate->dayOfWeek, $workDays ?? [1,2,3,4,5,6]);
                                $isFuture = $currentDayDate->isFuture();

                                $class = 'h-9 w-9 flex items-center justify-center rounded-full text-sm transition-colors ';
                                if ($isSelected) {
                                    $class .= 'bg-blue-600 text-white font-bold shadow-lg';
                                } elseif ($isToday) {
                                    $class .= 'ring-2 ring-blue-500';
                                } elseif ($isNonWorkDay || $isFuture) {
                                    $class .= 'text-gray-400 bg-gray-50 dark:bg-gray-700/50 cursor-not-allowed';
                                } elseif ($isCompleted) {
                                    $class .= 'bg-green-200 text-green-800 hover:bg-green-300 cursor-pointer';
                                } else {
                                    $class .= 'hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer';
                                }
                            @endphp
                            <div class="flex justify-center items-center">
                                @if ($isNonWorkDay || $isFuture)
                                    <div class="{{ $class }}"><span>{{ $day }}</span></div>
                                @else
                                    <a href="{{ route('attendances.index', ['date' => $currentDayDate->toDateString()]) }}"
                                        class="{{ $class }}">{{ $day }}</a>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Form Absensi --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-wrap justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                Form Absensi: <span
                                    class="text-indigo-600">{{ $selectedDate->isoFormat('dddd, D MMMM Y') }}</span>
                            </h3>
                            <div class="w-full sm:w-auto mt-2 sm:mt-0">
                                <x-text-input type="text" name="search_name" id="search_name" class="w-full sm:w-64"
                                    placeholder="Ketik untuk cari nama..." value="{{ $searchName ?? '' }}"
                                    hx-get="{{ route('attendances.search') }}" hx-trigger="keyup changed delay:300ms"
                                    hx-target="#attendance-table-body" hx-indicator=".htmx-indicator"
                                    hx-vals='{"date": "{{ $selectedDate->toDateString() }}"}' />
                                <span class="htmx-indicator ml-2 text-sm text-gray-500">Mencari...</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('attendances.store') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">
                            <div class="overflow-x-auto border-t">
                                <table class="w-full table-auto">
                                    <thead
                                        class="text-xs text-left text-gray-700 uppercase bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3">Nama Karyawan</th>
                                            <th class="px-4 py-3">Kehadiran</th>
                                            <th class="px-4 py-3 keterangan-header hidden">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-table-body">
                                        @include('attendances._employee_rows')
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-6">
                                <x-primary-button>Simpan Absensi</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function initAttendanceListeners() {
                const statusSelects = document.querySelectorAll('.attendance-status');
                statusSelects.forEach(select => {
                    select.removeEventListener('change', checkAllStatuses);
                    select.addEventListener('change', checkAllStatuses);
                });
                checkAllStatuses();
            }

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
            document.body.addEventListener('htmx:afterSwap', function(event) {
                if (event.detail.target.id === 'attendance-table-body') {
                    initAttendanceListeners();
                }
            });
        </script>
    @endpush
</x-app-layout>