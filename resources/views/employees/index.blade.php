<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('employees.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Karyawan Baru
                        </a>
                    </div>

                    {{-- Tabel sekarang hanya berisi a dan a kosong --}}
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500" id="employees-table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Tipe</th>
                                <th scope="col" class="px-6 py-3">Jabatan</th>
                                <th scope="col" class="px-6 py-3">Gaji Pokok</th>
                                <th scope="col" class="px-6 py-3">Transport</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- KOSONGKAN BAGIAN INI, AKAN DIISI OLEH JAVASCRIPT --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ open: false, employee: {} }" @open-employee-detail.window="employee = $event.detail.employeeData; open = true;"
        x-show="open" x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" style="display: none;">

        <div @click.away="open = false"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl transform transition-all"
            x-show="open" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

            {{-- Header Modal --}}
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200"
                    x-text="`Detail Karyawan: ${employee.nama}`"></h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/3 flex-shrink-0">
                        <img :src="employee.detail?.foto ? `/storage/${employee.detail.foto}` : 'https://via.placeholder.com/150'"
                            alt="Foto Karyawan" class="w-full h-auto rounded-lg object-cover aspect-square">
                        <div class="mt-4 text-center">
                            <span x-text="employee.jabatan"
                                class="block font-semibold text-gray-700 dark:text-gray-300"></span>
                            <span x-text="`Tipe: ${employee.tipe_karyawan}`"
                                class="block text-sm text-gray-500 dark:text-gray-400 capitalize"></span>
                        </div>
                    </div>

                    <div class="md:w-2/3 space-y-4">
                        {{-- Data Akun --}}
                        <div>
                            <h4 class="font-bold text-gray-700 dark:text-gray-300 border-b pb-1 mb-2">Informasi Akun
                            </h4>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="text-gray-900 dark:text-gray-100"
                                    x-text="employee.user?.email || 'Tidak ada'"></dd>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Status Akun</dt>
                                <dd class="font-semibold">
                                    {{-- Gunakan template untuk menampilkan status secara dinamis --}}
                                    <template x-if="employee.user">
                                        {{-- Jika user ada, cek status verifikasinya --}}
                                        <span x-text="employee.user.email_verified_at ? 'Aktif' : 'Belum Diverifikasi'"
                                            :class="{
                                                'text-green-600 dark:text-green-400': employee.user.email_verified_at,
                                                'text-yellow-600 dark:text-yellow-400': !employee.user.email_verified_at
                                            }">
                                        </span>
                                    </template>
                                    <template x-if="!employee.user">
                                        {{-- Jika user tidak ada sama sekali --}}
                                        <span class="text-red-600 dark:text-red-400">Belum Dibuat</span>
                                    </template>
                                </dd>
                            </dl>
                        </div>
                        {{-- Data Personal --}}
                        <div>
                            <h4 class="font-bold text-gray-700 dark:text-gray-300 border-b pb-1 mb-2">Informasi Personal
                            </h4>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <dt class="font-medium text-gray-500">Departemen</dt>
                                <dd class="text-gray-900" x-text="employee.departemen || 'N/A'"></dd>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Tanggal Masuk</dt>
                                <dd class="text-gray-900 dark:text-gray-100"
                                    x-text="employee.detail?.tanggal_masuk || 'N/A'"></dd>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Nomor HP</dt>
                                <dd class="text-gray-900 dark:text-gray-100" x-text="employee.detail?.no_hp || 'N/A'">
                                </dd>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Status Pernikahan</dt>
                                <dd class="text-gray-900 dark:text-gray-100"
                                    x-text="employee.detail?.status_pernikahan || 'N/A'"></dd>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Jumlah Anak</dt>
                                <dd class="text-gray-900 dark:text-gray-100"
                                    x-text="employee.detail?.jumlah_anak ?? 'N/A'"></dd>
                                <dt class="font-medium text-gray-500">Pendidikan</dt>
                                <dd class="text-gray-900"
                                    x-text="`${employee.detail?.pendidikan_terakhir || 'N/A'} - ${employee.detail?.jurusan || 'N/A'}`">
                                </dd>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Alamat</dt>
                                {{-- Dihapus: class col-span-2 pada <dd> dan textarea diganti dengan teks biasa --}}
                                <dd class="text-gray-900 dark:text-gray-100" x-text="employee.detail?.alamat || 'N/A'">
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 text-right">
                <x-secondary-button @click="open = false">Tutup</x-secondary-button>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Inisialisasi DataTables --}}
        <script>
            $(function() {
                $('#employees-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('employees.index') }}', // Route yang sama dengan halaman
                    dom: '<"flex justify-between items-center mb-4"lf>t<"flex justify-between items-center mt-4"ip>',
                    columnDefs: [{
                        className: "text-center", // Class bawaan datatables untuk text-align: center
                        targets: '_all' // Terapkan ke semua kolom
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'tipe_karyawan',
                            name: 'tipe_karyawan'
                        },
                        {
                            data: 'jabatan',
                            name: 'jabatan'
                        },
                        {
                            data: 'gaji_pokok',
                            name: 'gaji_pokok'
                        },
                        {
                            data: 'transport',
                            name: 'transport'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });

            // Script untuk SweetAlert konfirmasi hapus
        </script>
    @endpush


</x-app-layout>
