<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Event: {{ $event->nama_event }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- BAGIAN 1: FORM UNTUK MENAMBAH PESERTA INSENTIF (VERSI BARU) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Tambah Banyak Peserta Sekaligus</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih satu atau lebih karyawan, lalu tentukan jumlah insentif
                        yang akan diberikan kepada semua karyawan yang dipilih.</p>

                    <form method="POST" action="{{ route('events.incentives.store', $event->id) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                            {{-- DIUBAH: Multi-select Karyawan --}}
                            <div class="md:col-span-1">
                                <x-input-label for="employee_ids" value="Pilih Karyawan" />
                                {{-- Berikan id="select-karyawan" dan atribut multiple --}}
                                <select name="employee_ids[]" id="select-karyawan" class="block w-full mt-1"
                                    multiple="multiple" required>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_ids')" class="mt-2" />
                            </div>

                            {{-- Input Jumlah Insentif --}}
                            <div class="md:col-span-1">
                                <x-input-label for="jumlah_insentif" value="Jumlah Insentif (Rp)" />
                                <x-text-input id="jumlah_insentif" class="block mt-1 w-full" type="number"
                                    name="jumlah_insentif" required placeholder="500000" />
                                <x-input-error :messages="$errors->get('jumlah_insentif')" class="mt-2" />
                            </div>

                            {{-- Tombol Tambah --}}
                            <div class="md:col-span-1">
                                <x-primary-button>Tambah Karyawan Terpilih</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ... (Bagian Tabel Daftar Peserta tetap sama) ... --}}

            {{-- BAGIAN 2: TABEL DAFTAR PESERTA YANG SUDAH DITAMBAHKAN --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Daftar Penerima Insentif</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="text-xs text-left text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Nama Karyawan</th>
                                    <th class="px-4 py-3">Jumlah Insentif</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($event->incentives as $incentive)
                                    <tr class="border-b">
                                        <td class="px-4 py-3 font-medium">{{ $incentive->employee->nama }}</td>
                                        <td class="px-4 py-3">Rp
                                            {{ number_format($incentive->jumlah_insentif, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            {{-- Tombol Hapus memanggil route incentives.destroy --}}
                                            <x-action-button type="delete" :route="route('incentives.destroy', $incentive->id)" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center p-4">Belum ada karyawan yang ditambahkan
                                            ke event ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tombol Kembali --}}
            <div class="flex justify-start">
                <x-secondary-button :href="route('events.index')">Kembali ke Daftar Event</x-secondary-button>
            </div>
        </div>
    </div>

    {{-- Script untuk konfirmasi hapus --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Inisialisasi Select2 pada dropdown karyawan
                $('#select-karyawan').select2({
                    placeholder: 'Ketik untuk mencari nama...',
                    width: '100%' // Pastikan lebarnya penuh
                });
            });
        </script>
    @endpush
</x-app-layout>
