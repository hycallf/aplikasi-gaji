{{-- resources/views/deductions/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Potongan') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('deductions.create') }}"
                        class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                        + Tambah Potongan Manual
                    </a>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto" id="deductions-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Potongan</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
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
                $('#deductions-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('deductions.index') }}',
                    dom: '<"flex justify-between items-center mb-4"lf>t<"flex justify-between items-center mt-4"ip>',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_karyawan',
                            name: 'employee.nama'
                        },
                        {
                            data: 'tanggal_potongan',
                            name: 'tanggal_potongan'
                        },
                        {
                            data: 'jenis_potongan',
                            name: 'jenis_potongan'
                        },
                        {
                            data: 'jumlah_potongan',
                            name: 'jumlah_potongan'
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        className: "text-center",
                        targets: [0, 6]
                    }]
                });

                // Listener untuk konfirmasi hapus
                $('#deductions-table').on('submit', 'form', function(e) {
                    // Gunakan class form untuk membedakan jika ada form lain
                    if ($(this).find('button[type="submit"]').text().trim() === 'Hapus') {
                        confirmDelete(e);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
