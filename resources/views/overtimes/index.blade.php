<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Data Lembur Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 flex items-end gap-4">
                        <div>
                            <x-input-label for="date-filter" value="Pilih Tanggal" />
                            <input type="date" name="date" id="date-filter" value="{{ $selectedDate }}"
                                class="border-gray-300 rounded-md shadow-sm" hx-get="{{ route('overtimes.search') }}"
                                hx-trigger="change" hx-target="#overtime-table-body" hx-indicator=".htmx-indicator">
                        </div>
                        <div>
                            <x-input-label for="search_name" value="Cari Nama Karyawan" />
                            <x-text-input type="text" name="search_name" id="search_name" placeholder="Ketik nama..."
                                hx-get="{{ route('overtimes.search') }}" hx-trigger="keyup changed delay:500ms"
                                hx-target="#overtime-table-body" hx-indicator=".htmx-indicator" />
                        </div>
                        <span class="htmx-indicator">
                            <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </div>

                    <form method="POST" action="{{ route('overtimes.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $selectedDate }}">

                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="text-xs text-left text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">Nama Karyawan</th>
                                        <th class="px-4 py-3 w-24">Lembur?</th>
                                        <th class="px-4 py-3">Deskripsi Lembur</th>
                                        <th class="px-4 py-3">Upah Lembur (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody id="overtime-table-body">
                                    {{-- Load data awal dengan include partial view --}}
                                    @include('overtimes._employee_rows', [
                                        'employees' => \App\Models\Employee::where('status', 'aktif')->get(),
                                        'overtimes' => \App\Models\Overtime::where('tanggal_lembur', $selectedDate)->get()->keyBy('employee_id'),
                                    ])
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button>
                                Simpan Data Lembur
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Fungsi untuk menginisialisasi listener pada checkbox lembur
            function initOvertimeListeners() {
                const checkboxes = document.querySelectorAll('.overtime-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.removeEventListener('change', handleCheckboxChange); // Hapus listener lama
                    checkbox.addEventListener('change', handleCheckboxChange); // Tambah listener baru
                });
            }

            // Fungsi yang dijalankan saat checkbox berubah
            function handleCheckboxChange(event) {
                const employeeId = event.target.dataset.employeeId;
                const detailElements = document.querySelectorAll(`.overtime-details-${employeeId}`);

                if (event.target.checked) {
                    detailElements.forEach(el => el.classList.remove('hidden'));
                } else {
                    detailElements.forEach(el => el.classList.add('hidden'));
                }
            }

            // Inisialisasi listener saat halaman pertama kali dimuat
            initOvertimeListeners();

            // Setelah HTMX selesai menukar konten, inisialisasi ulang listener
            document.body.addEventListener('htmx:afterSwap', function(event) {
                // Kita menargetkan tbody agar listener hanya di-reset jika tbody yang di-update
                if (event.detail.target.id === 'overtime-table-body') {
                    initOvertimeListeners();
                }
            });
        </script>
    @endpush
</x-app-layout>
