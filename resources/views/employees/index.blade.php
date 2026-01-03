<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Karyawan & Dosen') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'karyawan' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tab Navigation --}}
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'karyawan'"
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'karyawan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'karyawan' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <i class="fa-solid fa-user-tie mr-2"></i>
                                Karyawan
                            </button>
                            <button @click="activeTab = 'dosen'"
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'dosen', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'dosen' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <i class="fa-solid fa-chalkboard-user mr-2"></i>
                                Dosen
                            </button>
                        </nav>
                    </div>

                    {{-- Tab Karyawan --}}
                    <div x-show="activeTab === 'karyawan'">
                        <div class="mb-4">
                            <a href="{{ route('employees.create') }}?tipe=karyawan"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fa-solid fa-plus mr-2"></i>Tambah Karyawan Baru
                            </a>
                        </div>

                        <table class="w-full text-sm text-left text-gray-500" id="karyawan-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">No</th>
                                    <th scope="col" class="px-6 py-3">Nama</th>
                                    <th scope="col" class="px-6 py-3">Jabatan</th>
                                    <th scope="col" class="px-6 py-3">Departemen</th>
                                    <th scope="col" class="px-6 py-3">Gaji Pokok</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    {{-- Tab Dosen --}}
                    <div x-show="activeTab === 'dosen'" style="display: none;">
                        <div class="mb-4 flex justify-between items-center">
                            <a href="{{ route('employees.create') }}?tipe=dosen"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fa-solid fa-plus mr-2"></i>Tambah Dosen Baru
                            </a>
                            <a href="{{ route('enrollments.index') }}"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fa-solid fa-book-open mr-2"></i>Kelola Enrollment
                            </a>
                        </div>

                        <table class="w-full text-sm text-left text-gray-500" id="dosen-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">No</th>
                                    <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                                    <th scope="col" class="px-6 py-3">NIDN</th>
                                    <th scope="col" class="px-6 py-3">Status Dosen</th>
                                    <th scope="col" class="px-6 py-3">Departemen</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Employee (shared) --}}
    <div x-data="{ open: false, employee: {} }" @open-employee-detail.window="employee = $event.detail.employeeData; open = true;"
        x-show="open" x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="open = false" class="bg-white rounded-lg shadow-xl w-full max-w-3xl transform transition-all"
            x-show="open">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800"
                    x-text="`Detail: ${employee.nama_lengkap || employee.nama}`"></h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                {{-- Content will be similar to existing modal --}}
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/3">
                        <img :src="employee.detail?.foto ? `/storage/${employee.detail.foto}` : 'https://via.placeholder.com/150'"
                            alt="Foto" class="w-full h-auto rounded-lg">
                        <div class="mt-4 text-center">
                            <span x-text="employee.jabatan" class="block font-semibold"></span>
                            <span x-text="employee.tipe_karyawan" class="block text-sm text-gray-500 capitalize"></span>
                        </div>
                    </div>
                    <div class="md:w-2/3 space-y-4">
                        {{-- Data lengkap seperti sebelumnya --}}
                        <div>
                            <h4 class="font-bold border-b pb-1 mb-2">Informasi Personal</h4>
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                <dt class="font-medium text-gray-500">Email</dt>
                                <dd x-text="employee.user?.email || 'N/A'"></dd>
                                <dt class="font-medium text-gray-500">No HP</dt>
                                <dd x-text="employee.detail?.no_hp || 'N/A'"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 text-right">
                <x-secondary-button @click="open = false">Tutup</x-secondary-button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                // DataTable untuk Karyawan
                let karyawanTable = $('#karyawan-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('employees.index') }}',
                        data: {
                            tipe: 'karyawan'
                        }
                    },
                    columnDefs: [{
                        className: "text-center",
                        targets: '_all'
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'jabatan',
                            name: 'jabatan'
                        },
                        {
                            data: 'departemen',
                            name: 'departemen'
                        },
                        {
                            data: 'gaji_pokok',
                            name: 'gaji_pokok'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                // DataTable untuk Dosen
                let dosenTable = $('#dosen-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('employees.index') }}',
                        data: {
                            tipe: 'dosen'
                        }
                    },
                    columnDefs: [{
                        className: "text-center",
                        targets: '_all'
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_lengkap',
                            name: 'nama_lengkap'
                        },
                        {
                            data: 'nidn',
                            name: 'nidn',
                            defaultContent: '-'
                        },
                        {
                            data: 'status_dosen',
                            name: 'status_dosen',
                            defaultContent: '-'
                        },
                        {
                            data: 'departemen',
                            name: 'departemen'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>
