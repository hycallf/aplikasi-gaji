<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Pemberian Insentif</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('incentives.create') }}"
                            class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Insentif Baru
                        </a>
                        <a href="{{ route('events.index') }}"
                            class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kelola Jenis Event
                        </a>
                    </div>

                    {{-- DITAMBAHKAN: Filter Tanggal --}}
                    <div class="mb-4 flex items-end gap-4 border p-4 rounded-md">
                        <div>
                            <label for="start_date_filter" class="block text-sm font-medium text-gray-700">Filter dari
                                Tanggal</label>
                            <input type="date" id="start_date_filter" name="start_date_filter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="end_date_filter" class="block text-sm font-medium text-gray-700">Sampai
                                Tanggal</label>
                            <input type="date" id="end_date_filter" name="end_date_filter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <button id="filter-btn"
                                class="bg-indigo-500 text-white px-4 py-2 rounded-md shadow-sm">Filter</button>
                            <button id="reset-btn"
                                class="bg-gray-500 text-white px-4 py-2 rounded-md shadow-sm">Reset</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-auto" id="incentives-table">
                            <thead>
                                <tr>
                                    <th>
                                        No
                                    </th>
                                    <th>Tanggal</th>
                                    <th>karyawan</th>
                                    <th>event</th>
                                    <th>insentif</th>
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
                // Inisialisasi DataTables
                var table = $('#incentives-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('incentives.index') }}',
                        // Kirim data filter tanggal setiap kali request
                        data: function(d) {
                            d.start_date_filter = $('#start_date_filter').val();
                            d.end_date_filter = $('#end_date_filter').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal_insentif',
                            name: 'tanggal_insentif'
                        },
                        {
                            data: 'nama_karyawan',
                            name: 'employee.nama'
                        },
                        {
                            data: 'nama_event',
                            name: 'event.nama_event'
                        },
                        {
                            data: 'jumlah_insentif',
                            name: 'jumlah_insentif'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: '<"flex justify-between items-center mb-4"lf>t<"flex justify-between items-center mt-4"ip>',
                    columnDefs: [{
                        className: "text-center", // Class bawaan datatables untuk text-align: center
                        targets: '_all' // Terapkan ke semua kolom
                    }],
                });

                // Event listener untuk tombol filter
                $('#filter-btn').click(function() {
                    table.draw(); // Gambar ulang tabel dengan data baru
                });

                // Event listener untuk tombol reset
                $('#reset-btn').click(function() {
                    $('#start_date_filter').val('');
                    $('#end_date_filter').val('');
                    table.draw(); // Gambar ulang tabel tanpa filter
                });

                // Listener untuk konfirmasi hapus (tetap sama)
                $('#incentives-table').on('submit', '.delete-form', function(e) {
                    confirmDelete(e);
                });
            });
        </script>
    @endpush
</x-app-layout>
