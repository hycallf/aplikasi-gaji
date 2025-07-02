<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Mata Kuliah
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('matkuls.create') }}"
                            class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Mata Kuliah Baru
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-auto" id="matkuls-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3" style="width: 50px;">No</th>
                                    <th class="px-6 py-3">Nama Mata Kuliah</th>
                                    <th class="px-6 py-3" style="width: 100px;">SKS</th>
                                    <th class="px-6 py-3" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                $('#matkuls-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('matkuls.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_matkul',
                            name: 'nama_matkul'
                        },
                        {
                            data: 'sks',
                            name: 'sks'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    columnDefs: [{
                        className: "text-center",
                        targets: [0, 2, 3]
                    }],
                    dom: '<"flex justify-between items-center mb-4"lf>t<"flex justify-between items-center mt-4"ip>',
                });
                $('#matkuls-table').on('submit', 'form.delete-form', function(e) {
                    confirmDelete(e);
                });
            });
        </script>
    @endpush
</x-app-layout>
