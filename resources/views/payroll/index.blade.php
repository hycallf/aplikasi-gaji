<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proses & Laporan Gaji</h2>
    </x-slot>

    <div class="py-12" x-data="payrollPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Proses & Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-wrap items-end gap-4">
                        {{-- Form untuk memproses gaji --}}
                        <form id="process-form" method="POST" action="{{ route('payroll.process') }}"
                            class="flex items-end gap-4">
                            @csrf
                            <div>
                                <x-input-label for="month" value="Bulan" />
                                <select name="month" id="month" class="border-gray-300 rounded-md shadow-sm">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}"
                                            {{ $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label for="year" value="Tahun" />
                                <select name="year" id="year" class="border-gray-300 rounded-md shadow-sm">
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div><x-primary-button type="submit">Proses Gaji</x-primary-button></div>
                        </form>
                        {{-- Tombol Cetak Laporan --}}
                        <div>
                            <a href="#" id="print-report-btn"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white ...">Cetak Laporan</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel DataTables --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <table class="w-full table-auto" id="payroll-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Gaji Pokok</th>
                                <th class="text-right">Tunj. Transport</th>
                                <th class="text-right">Upah Lembur</th>
                                <th class="text-right">Insentif</th>
                                <th class="text-right">Potongan</th>
                                <th class="text-right">Gaji Bersih</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex ..." style="display: none;">
            <div @click.away="modalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-xl font-bold" x-text="modalTitle"></h3>
                    <button @click="modalOpen = false">&times;</button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto" x-html="modalContent">
                    {{-- Konten detail akan dimuat di sini --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function payrollPage() {
                return {
                    modalOpen: false,
                    modalTitle: '',
                    modalContent: 'Memuat...',
                    async showDetails(payrollId, type) {
                        this.modalOpen = true;
                        this.modalTitle = `Detail ${type.charAt(0).toUpperCase() + type.slice(1)}`;
                        this.modalContent = 'Memuat...';

                        try {
                            let response = await fetch(`/payroll/${payrollId}/details/${type}`);
                            this.modalContent = await response.text();
                        } catch (error) {
                            this.modalContent = '<p class="text-red-500">Gagal memuat detail.</p>';
                        }
                    }
                }
            }

            $(function() {
                var table = $('#payroll-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('payroll.index') }}',
                        data: function(d) {
                            d.month = $('#month').val();
                            d.year = $('#year').val();
                        }
                    },
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
                            data: 'gaji_pokok',
                            name: 'gaji_pokok',
                            className: 'text-right'
                        },
                        {
                            data: 'transport',
                            name: 'total_tunjangan_transport',
                            className: 'text-right'
                        },
                        {
                            data: 'lembur',
                            name: 'total_upah_lembur',
                            className: 'text-right'
                        },
                        {
                            data: 'insentif',
                            name: 'total_insentif',
                            className: 'text-right'
                        },
                        {
                            data: 'potongan',
                            name: 'total_potongan',
                            className: 'text-right'
                        },
                        {
                            data: 'gaji_bersih',
                            name: 'gaji_bersih',
                            className: 'text-right'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                    ]
                });

                // Otomatis reload tabel saat form proses di-submit (setelah redirect)
                // Atau saat filter berubah (jika Anda ingin filter live)
                $('#process-form').submit(function(e) {
                    // Biarkan form submit seperti biasa
                });
            });

            $('#print-report-btn').on('click', function(e) {
                e.preventDefault();
                var month = $('#month').val();
                var year = $('#year').val();
                // Buka di tab baru
                window.open(`{{ route('report.payroll') }}?month=${month}&year=${year}`, '_blank');
            });
        </script>
    @endpush
</x-app-layout>
