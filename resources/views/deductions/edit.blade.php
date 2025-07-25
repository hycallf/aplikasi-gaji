<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Judul dinamis dengan nama karyawan --}}
            Edit Potongan untuk {{ $deduction->employee->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('deductions.update', $deduction->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Pilih Karyawan --}}
                        <div class="mt-4">
                            <x-input-label for="employee_id" value="Karyawan" />
                            <select name="employee_id" id="employee_id"
                                class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                                required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($employees as $employee)
                                    {{-- Menandai karyawan yang sedang diedit sebagai 'selected' --}}
                                    <option value="{{ $employee->id }}"
                                        {{ old('employee_id', $deduction->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                        </div>

                        {{-- Tanggal Potongan --}}
                        <div class="mt-4">
                            <x-input-label for="tanggal_potongan" value="Tanggal Potongan" />
                            <x-text-input id="tanggal_potongan" class="block mt-1 w-full" type="date"
                                name="tanggal_potongan" :value="old('tanggal_potongan', $deduction->tanggal_potongan)" required />
                            <x-input-error :messages="$errors->get('tanggal_potongan')" class="mt-2" />
                        </div>

                        {{-- Jenis Potongan --}}
                        <div class="mt-4">
                            <x-input-label for="jenis_potongan" value="Jenis Potongan" />
                            <x-text-input id="jenis_potongan" class="block mt-1 w-full" type="text"
                                name="jenis_potongan" :value="old('jenis_potongan', $deduction->jenis_potongan)" required
                                placeholder="Cth: Kasbon, Denda Keterlambatan" />
                            <x-input-error :messages="$errors->get('jenis_potongan')" class="mt-2" />
                        </div>

                        {{-- Jumlah Potongan --}}
                        <div class="mt-4">
                            <x-input-label for="jumlah_potongan" value="Jumlah Potongan (Rp)" />
                            <x-text-input id="jumlah_potongan" class="block mt-1 w-full" type="number"
                                name="jumlah_potongan" :value="old('jumlah_potongan', $deduction->jumlah_potongan)" required placeholder="50000" />
                            <x-input-error :messages="$errors->get('jumlah_potongan')" class="mt-2" />
                        </div>

                        {{-- Keterangan --}}
                        <div class="mt-4">
                            <x-input-label for="keterangan" value="Keterangan (Opsional)" />
                            <textarea name="keterangan" id="keterangan" rows="3"
                                class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('keterangan', $deduction->keterangan) }}</textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('deductions.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update Potongan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
