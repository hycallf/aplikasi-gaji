<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Kehadiran Dosen per Mata Kuliah
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('dosen.attendances.index') }}" class="mb-6 pb-6 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-2">
                                <x-input-label for="employee_id" value="Pilih Dosen" />
                                <select name="employee_id" id="employee_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" @selected(optional($selectedDosen)->id == $dosen->id)>
                                            {{ $dosen->nama }}
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
                                <x-primary-button type="submit">Tampilkan</x-primary-button>
                            </div>
                        </div>
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                    </form>

                    {{-- Form untuk menyimpan data hanya jika dosen sudah dipilih --}}
                    @if ($selectedDosen)
                        <h3 class="text-lg font-bold mb-4">Input Jumlah Pertemuan untuk: <span
                                class="text-indigo-600">{{ $selectedDosen->nama }}</span></h3>
                        <form method="POST" action="{{ route('dosen.attendances.store') }}">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $selectedDosen->id }}">
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <input type="hidden" name="year" value="{{ $selectedYear }}">

                            <div class="space-y-4">
                                @forelse ($matkuls as $matkul)
                                    <div class="grid grid-cols-3 gap-4 items-center">
                                        <div class="col-span-2">
                                            <label class="font-medium">{{ $matkul->nama_matkul }} ({{ $matkul->sks }}
                                                SKS)</label>
                                        </div>
                                        <div>
                                            <x-text-input type="number" name="pertemuan[{{ $matkul->id }}]"
                                                class="w-full text-center"
                                                value="{{ $existingAttendances[$matkul->id] ?? 0 }}" />
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-500 py-4">Dosen ini belum di-assign ke mata kuliah
                                        manapun.</p>
                                @endforelse
                            </div>

                            @if ($matkuls->isNotEmpty())
                                <div class="flex justify-end mt-6 border-t pt-6">
                                    <x-primary-button>Simpan Kehadiran</x-primary-button>
                                </div>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
