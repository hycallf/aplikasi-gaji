<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Kalender Absensi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @php
                        // Logika untuk navigasi bulan
                        $prevMonth = $currentDate->copy()->subMonth();
                        $nextMonth = $currentDate->copy()->addMonth();
                        $daysInMonth = $currentDate->daysInMonth;
                        $firstDayOfMonth = $currentDate->copy()->startOfMonth()->dayOfWeek;
                        // Konversi agar Senin = 0
                        $startBlankDays = $firstDayOfMonth == 0 ? 6 : $firstDayOfMonth - 1;
                    @endphp

                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('attendances.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                            class="px-4 py-2 bg-gray-200 rounded-lg">&lt; Sebelumnya</a>
                        <h3 class="text-2xl font-bold">{{ $currentDate->isoFormat('MMMM YYYY') }}</h3>
                        <a href="{{ route('attendances.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                            class="px-4 py-2 bg-gray-200 rounded-lg">Berikutnya &gt;</a>
                    </div>

                    <div class="grid grid-cols-7 gap-2">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                            <div class="text-center font-bold p-2">{{ $day }}</div>
                        @endforeach

                        @for ($i = 0; $i < $startBlankDays; $i++)
                            <div></div>
                        @endfor

                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = $currentDate->copy()->setDay($day);
                                $isCompleted = in_array($day, $completedDates);
                                $isSunday = $date->isSunday();
                                $isFuture = $date->isFuture();

                                $class = 'p-4 border rounded-lg h-24 flex flex-col justify-between ';
                                if ($isSunday) {
                                    $class .= 'bg-gray-100 dark:bg-gray-700 text-gray-400';
                                } elseif ($isCompleted) {
                                    $class .= 'bg-green-100 dark:bg-green-900 border-green-300';
                                } else {
                                    $class .= 'bg-white dark:bg-gray-800';
                                }
                            @endphp
                            <div class="{{ $class }}">
                                <span class="font-bold">{{ $day }}</span>
                                @if (!$isSunday && !$isFuture)
                                    <a href="{{ route('attendances.index', ['date' => $date->toDateString()]) }}"
                                        class="text-xs text-center bg-blue-500 text-white rounded-full px-2 py-1 hover:bg-blue-600 self-center">
                                        Input
                                    </a>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
