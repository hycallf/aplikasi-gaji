<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Jenis Event & Insentif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('events.create') }}"
                            class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Jenis Event Baru
                        </a>
                        <a href="{{ route('incentives.index') }}"
                            class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali ke Riwayat Insentif
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-auto" id="events-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3" style="width: 50px;">No</th>
                                    <th class="px-6 py-3">Nama Jenis Event</th>
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
                $('#events-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('events.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_event',
                            name: 'nama_event'
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
                        targets: [0, 2]
                    }]
                });
                $('#events-table').on('submit', 'form.delete-form', function(e) {
                    confirmDelete(e);
                });
            });
        </script>
    @endpush
</x-app-layout>
