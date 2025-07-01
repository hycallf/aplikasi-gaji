<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Rekap Absensi & Lembur Bulanan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Form Rekapitulasi</h3>
                    <form method="POST" action="{{ route('recap.store') }}">
                        @csrf

                        {{-- Filter Karyawan & Periode --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 pb-6 border-b">
                            <div>
                                <x-input-label for="employee_id" value="Pilih Karyawan" />
                                <select name="employee_id" id="employee_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="month" value="Bulan" />
                                <select name="month" id="month"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label for="year" value="Tahun" />
                                <select name="year" id="year"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    @for ($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Input Rekap --}}
                        <p class="font-semibold mb-2">Input Jumlah Hari Kehadiran:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="hadir" value="Hadir" />
                                <x-text-input id="hadir" class="block mt-1 w-full" type="number" name="hadir"
                                    value="0" required />
                            </div>
                            <div>
                                <x-input-label for="sakit" value="Sakit" />
                                <x-text-input id="sakit" class="block mt-1 w-full" type="number" name="sakit"
                                    value="0" required />
                            </div>
                            <div>
                                <x-input-label for="izin" value="Izin" />
                                <x-text-input id="izin" class="block mt-1 w-full" type="number" name="izin"
                                    value="0" required />
                            </div>
                            <div>
                                <x-input-label for="pulang_awal" value="Pulang Awal" />
                                <x-text-input id="pulang_awal" class="block mt-1 w-full" type="number"
                                    name="pulang_awal" value="0" required />
                            </div>
                        </div>

                        <p class="font-semibold mb-2 mt-6">Input Akumulasi Lembur:</p>
                        <div>
                            <x-input-label for="total_lembur" value="Total Upah Lembur (Rp)" />
                            <x-text-input id="total_lembur" class="block mt-1 w-full" type="number" name="total_lembur"
                                value="0" />
                        </div>

                        <div class="flex justify-end mt-8">
                            <x-primary-button>Simpan Rekap</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
