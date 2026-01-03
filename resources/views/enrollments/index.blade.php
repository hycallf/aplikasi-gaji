<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Enrollment Dosen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                        <a href="{{ route('enrollments.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Enrollment Baru
                        </a>

                        <div class="flex items-center gap-2">
                            <label for="filter_academic_year" class="text-sm font-medium text-gray-700">Filter Tahun
                                Ajaran:</label>
                            <select id="filter_academic_year" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Tahun Ajaran Aktif</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <table class="w-full text-sm text-left text-gray-500" id="enrollments-table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Nama Dosen</th>
                                <th scope="col" class="px-6 py-3">Mata Kuliah (SKS)</th>
                                <th scope="col" class="px-6 py-3">Kelas</th>
                                <th scope="col" class="px-6 py-3">Jml Mahasiswa</th>
                                <th scope="col" class="px-6 py-3">Periode</th>
                                <th scope="col" class="px-6 py-3" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                let table = $('#enrollments-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('enrollments.index') }}',
                        data: function(d) {
                            d.academic_year_id = $('#filter_academic_year').val();
                        }
                    },
                    columnDefs: [{
                        className: "text-center",
                        targets: '_all'
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'dosen_nama',
                            name: 'dosen_nama'
                        },
                        {
                            data: 'matkul_info',
                            name: 'matkul_info'
                        },
                        {
                            data: 'kelas',
                            name: 'kelas',
                            defaultContent: '-'
                        },
                        {
                            data: 'jumlah_mahasiswa',
                            name: 'jumlah_mahasiswa'
                        },
                        {
                            data: 'periode',
                            name: 'periode'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                $('#filter_academic_year').on('change', function() {
                    table.ajax.reload();
                });
            });
        </script>
    @endpush
</x-app-layout>
