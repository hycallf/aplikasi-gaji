<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Kehadiran Dosen per Enrollment
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Alert Success --}}
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <i class="fa-solid fa-check-circle mr-2"></i>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Alert Error --}}
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <i class="fa-solid fa-exclamation-circle mr-2"></i>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Error!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Error dari Controller --}}
                    @if (isset($error))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <i class="fa-solid fa-exclamation-circle mr-2"></i>
                            {{ $error }}
                        </div>
                    @endif

                    {{-- Info Tahun Ajaran Aktif --}}
                    @if ($activeAcademicYear)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-graduation-cap text-blue-600 mr-3 text-xl"></i>
                                    <div>
                                        <p class="font-semibold text-blue-800">Tahun Ajaran Aktif</p>
                                        <p class="text-sm text-blue-600">{{ $activeAcademicYear->nama_lengkap }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('enrollments.index') }}"
                                    class="text-xs bg-white text-blue-600 px-3 py-1 rounded border border-blue-300 hover:bg-blue-50">
                                    <i class="fa-solid fa-cog mr-1"></i> Kelola Enrollment
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Form Pilih Dosen dan Periode --}}
                    <form method="GET" action="{{ route('dosen.attendances.index') }}" class="mb-6 pb-6 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-2">
                                <x-input-label for="employee_id" value="Pilih Dosen" />
                                <select name="employee_id" id="employee_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" @selected(optional($selectedDosen)->id == $dosen->id)>
                                            {{ $dosen->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="month" value="Bulan" />
                                <select name="month" id="month"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected($selectedMonth == $m)>
                                            {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-primary-button type="submit">
                                    <i class="fa-solid fa-search mr-2"></i>
                                    Tampilkan
                                </x-primary-button>
                            </div>
                        </div>
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                    </form>

                    {{-- Form Input Kehadiran --}}
                    @if ($selectedDosen)
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-2">
                                Dosen: <span class="text-indigo-600">{{ $selectedDosen->nama_lengkap }}</span>
                            </h3>
                            <p class="text-sm text-gray-600">
                                Periode:
                                {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->isoFormat('MMMM YYYY') }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('dosen.attendances.store') }}">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $selectedDosen->id }}">
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <input type="hidden" name="year" value="{{ $selectedYear }}">

                            <div class="space-y-3">
                                @forelse ($enrollments as $enrollment)
                                    <div
                                        class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-800">
                                                    {{ $enrollment->matkul->nama_matkul }}
                                                    <span class="text-sm text-gray-500">({{ $enrollment->matkul->sks }}
                                                        SKS)</span>
                                                </h4>
                                                @if ($enrollment->kelas)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <i class="fa-solid fa-users mr-1"></i>
                                                        Kelas: {{ $enrollment->kelas }}
                                                        @if ($enrollment->jumlah_mahasiswa > 0)
                                                            ({{ $enrollment->jumlah_mahasiswa }} mahasiswa)
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <label class="text-sm text-gray-600">Jumlah Pertemuan:</label>
                                                <x-text-input 
                                                    type="number" 
                                                    name="pertemuan[{{ $enrollment->id }}]"
                                                    class="w-20 text-center"
                                                    value="{{ $existingAttendances[$enrollment->id]->jumlah_pertemuan ?? 0 }}"
                                                    min="0" 
                                                    required />
                                            </div>
                                        </div>

                                        @if ($enrollment->catatan)
                                            <p class="text-xs text-gray-500 bg-gray-50 p-2 rounded">
                                                <i class="fa-solid fa-info-circle mr-1"></i>
                                                {{ $enrollment->catatan }}
                                            </p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fa-solid fa-inbox text-4xl mb-3 text-gray-300"></i>
                                        <p class="font-medium">Dosen ini belum memiliki enrollment</p>
                                        <p class="text-sm mt-1">Silakan tambahkan enrollment terlebih dahulu untuk tahun ajaran aktif</p>
                                        <a href="{{ route('enrollments.create') }}"
                                            class="inline-block mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                            <i class="fa-solid fa-plus mr-1"></i> Tambah Enrollment
                                        </a>
                                    </div>
                                @endforelse
                            </div>

                            @if ($enrollments->isNotEmpty())
                                <div class="flex justify-between items-center mt-6 pt-6 border-t">
                                    <div class="text-sm text-gray-600">
                                        <i class="fa-solid fa-calculator mr-1"></i>
                                        Total Enrollment: <span class="font-bold">{{ $enrollments->count() }}</span>
                                    </div>
                                    <x-primary-button>
                                        <i class="fa-solid fa-save mr-2"></i>
                                        Simpan Kehadiran
                                    </x-primary-button>
                                </div>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>