<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Rekap Absensi & Lembur Bulanan
        </h2>
    </x-slot>

    <div class="py-12" x-data="monthlyRecapForm({{ json_encode($existingData) }})">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Form Load Data --}}
                    <form method="GET" action="{{ route('recap.index') }}" class="mb-6 pb-6 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <x-input-label for="load_employee_id" value="Pilih Karyawan" />
                                <select name="employee_id" id="load_employee_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="load_month" value="Bulan" />
                                <select name="month" id="load_month"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}"
                                            {{ request('month', date('m')) == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label for="load_year" value="Tahun" />
                                <select name="year" id="load_year"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                    @for ($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}"
                                            {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-primary-button type="submit">
                                    <i class="fa-solid fa-search mr-2"></i>Load Data
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    {{-- Form Input/Update --}}
                    <form method="POST" action="{{ route('recap.store') }}" @submit="prepareSubmit">
                        @csrf

                        <input type="hidden" name="employee_id" x-model="selectedEmployeeId">
                        <input type="hidden" name="month" x-model="selectedMonth">
                        <input type="hidden" name="year" x-model="selectedYear">

                        {{-- Kalender Mini untuk Visual --}}
                        <div x-show="employeeSelected" class="mb-6 pb-6 border-b">
                            <h3 class="font-bold mb-3">
                                Kalender: <span class="text-indigo-600" x-text="calendarTitle"></span>
                            </h3>
                            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                <template x-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']">
                                    <div class="font-bold p-2" x-text="day"></div>
                                </template>
                                <template x-for="i in startBlankDays">
                                    <div></div>
                                </template>
                                <template x-for="day in daysInMonth" :key="day">
                                    <div class="p-2 border rounded cursor-pointer hover:bg-gray-100"
                                        :class="{
                                            'bg-gray-200 text-gray-400 cursor-not-allowed': isSunday(day),
                                            'bg-red-100 border-red-300': selectedDates.sakit.includes(day),
                                            'bg-yellow-100 border-yellow-300': selectedDates.izin.includes(day),
                                            'bg-orange-100 border-orange-300': selectedDates.pulang_awal.includes(day)
                                        }"
                                        @click="!isSunday(day) && toggleDateSelection(day)" x-text="day">
                                    </div>
                                </template>
                            </div>
                            <div class="flex gap-4 mt-4 text-xs">
                                <div class="flex items-center gap-1">
                                    <div class="w-4 h-4 bg-red-100 border border-red-300 rounded"></div>
                                    <span>Sakit</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded"></div>
                                    <span>Izin</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-4 h-4 bg-orange-100 border border-orange-300 rounded"></div>
                                    <span>Pulang Awal</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-4 h-4 bg-gray-200 border border-gray-300 rounded"></div>
                                    <span>Minggu (Libur)</span>
                                </div>
                            </div>
                        </div>

                        {{-- Mode Selection --}}
                        <div x-show="employeeSelected" class="mb-6">
                            <label class="block font-semibold mb-2">Mode Pilih Tanggal:</label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="selectionMode" value="sakit"
                                        class="form-radio text-red-500">
                                    <span class="ml-2">Sakit</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="selectionMode" value="izin"
                                        class="form-radio text-yellow-500">
                                    <span class="ml-2">Izin</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="selectionMode" value="pulang_awal"
                                        class="form-radio text-orange-500">
                                    <span class="ml-2">Pulang Awal</span>
                                </label>
                                <button type="button" @click="clearSelection"
                                    class="ml-4 text-sm text-red-600 hover:underline">
                                    <i class="fa-solid fa-times-circle mr-1"></i>Reset Pilihan
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Pilih mode, lalu klik tanggal di kalender. Hadir otomatis terisi untuk hari kerja yang
                                tidak dipilih.
                            </p>
                        </div>

                        {{-- Summary --}}
                        <div x-show="employeeSelected" class="grid grid-cols-4 gap-4 mb-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600" x-text="totalHadir"></div>
                                <div class="text-sm text-gray-600">Hadir</div>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-red-600" x-text="selectedDates.sakit.length">
                                </div>
                                <div class="text-sm text-gray-600">Sakit</div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-600" x-text="selectedDates.izin.length">
                                </div>
                                <div class="text-sm text-gray-600">Izin</div>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-orange-600"
                                    x-text="selectedDates.pulang_awal.length"></div>
                                <div class="text-sm text-gray-600">Pulang Awal</div>
                            </div>
                        </div>

                        {{-- Hidden Inputs --}}
                        <input type="hidden" name="hadir" :value="totalHadir">
                        <input type="hidden" name="sakit" :value="selectedDates.sakit.length">
                        <input type="hidden" name="izin" :value="selectedDates.izin.length">
                        <input type="hidden" name="pulang_awal" :value="selectedDates.pulang_awal.length">

                        {{-- Lembur --}}
                        <div x-show="employeeSelected" class="mb-6">
                            <p class="font-semibold mb-2">Input Akumulasi Lembur:</p>
                            <div>
                                <x-input-label for="total_lembur" value="Total Upah Lembur (Rp)" />
                                <x-text-input id="total_lembur" class="block mt-1 w-full" type="number"
                                    name="total_lembur" x-model="totalLembur" min="0" />
                            </div>
                        </div>

                        <div x-show="employeeSelected" class="flex justify-end mt-8">
                            <x-primary-button>
                                <i class="fa-solid fa-save mr-2"></i>
                                Simpan Rekap
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function monthlyRecapForm() {
                // Pass data dari PHP ke JavaScript dengan aman
                const existingData = {!! json_encode($existingData) !!};
                const nonWorkingDays = {!! json_encode($nonWorkingDays ?? []) !!};
                const workDays = {!! json_encode($workDays ?? [1, 2, 3, 4, 5, 6]) !!};

                return {
                    employeeSelected: false,
                    selectedEmployeeId: existingData?.employee_id || '',
                    selectedMonth: existingData?.month || {{ request('month', date('m')) }},
                    selectedYear: existingData?.year || {{ request('year', date('Y')) }},
                    selectionMode: 'sakit',
                    selectedDates: {
                        sakit: existingData?.dates_by_status?.sakit || [],
                        izin: existingData?.dates_by_status?.izin || [],
                        pulang_awal: existingData?.dates_by_status?.pulang_awal || []
                    },
                    totalLembur: existingData?.total_overtime || 0,
                    daysInMonth: 31,
                    startBlankDays: 0,
                    nonWorkingDays: nonWorkingDays, // Dari PHP
                    workDays: workDays, // Dari PHP

                    init() {
                        if (existingData) {
                            this.employeeSelected = true;
                            this.updateCalendar();
                        }
                    },

                    get calendarTitle() {
                        const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        return `${months[this.selectedMonth]} ${this.selectedYear}`;
                    },

                    get totalWorkDays() {
                        return this.daysInMonth - this.nonWorkingDays.length;
                    },

                    get totalHadir() {
                        const totalAbsent = this.selectedDates.sakit.length +
                            this.selectedDates.izin.length +
                            this.selectedDates.pulang_awal.length;
                        return Math.max(0, this.totalWorkDays - totalAbsent);
                    },

                    updateCalendar() {
                        const date = new Date(this.selectedYear, this.selectedMonth - 1, 1);
                        this.daysInMonth = new Date(this.selectedYear, this.selectedMonth, 0).getDate();

                        let firstDay = date.getDay();
                        this.startBlankDays = firstDay === 0 ? 6 : firstDay - 1;

                        // Update non-working days based on work days setting
                        this.nonWorkingDays = [];
                        for (let day = 1; day <= this.daysInMonth; day++) {
                            if (this.isNonWorkDay(day)) {
                                this.nonWorkingDays.push(day);
                            }
                        }

                        if (!existingData || !existingData.employee_id) {
                            this.clearSelection();
                        }
                    },

                    isNonWorkDay(day) {
                        const date = new Date(this.selectedYear, this.selectedMonth - 1, day);
                        const dayOfWeek = date.getDay();
                        return !this.workDays.includes(dayOfWeek);
                    },

                    // Alias untuk backward compatibility dengan template
                    isSunday(day) {
                        return this.isNonWorkDay(day);
                    },

                    toggleDateSelection(day) {
                        if (this.isNonWorkDay(day)) return;

                        // Remove day from all categories first
                        Object.keys(this.selectedDates).forEach(key => {
                            const index = this.selectedDates[key].indexOf(day);
                            if (index > -1) {
                                this.selectedDates[key].splice(index, 1);
                            }
                        });

                        // Add to selected mode
                        this.selectedDates[this.selectionMode].push(day);
                    },

                    clearSelection() {
                        this.selectedDates = {
                            sakit: [],
                            izin: [],
                            pulang_awal: []
                        };
                    },

                    prepareSubmit() {
                        // Form will auto-submit
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
