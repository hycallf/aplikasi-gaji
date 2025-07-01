<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Insentif Baru</h2>
    </x-slot>

    {{-- "Detektor Error" di paling atas --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4"
                    role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan validasi:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="py-12 pt-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('incentives.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="event_id" value="Jenis Event/Insentif" />
                            <select name="event_id" id="event_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Jenis Event --</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}"
                                        {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->nama_event }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="tanggal_insentif" value="Pilih Satu atau Lebih Tanggal Insentif" />
                            <input id="tanggal-insentif-display"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-50" type="text"
                                readonly placeholder="Klik untuk memilih tanggal...">
                            <div id="tanggal-insentif-container">
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="employee_ids" value="Pilih Karyawan Penerima" />
                            <select name="employee_ids[]" id="select-karyawan" class="block w-full mt-1"
                                multiple="multiple" required></select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="jumlah_insentif" value="Jumlah Insentif per Karyawan (Rp)" />
                            <x-text-input id="jumlah_insentif" class="block mt-1 w-full" type="number"
                                name="jumlah_insentif" :value="old('jumlah_insentif')" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="deskripsi" value="Deskripsi (Opsional)" />
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('incentives.index')">Kembali</x-secondary-button>
                            <x-primary-button>Simpan Insentif</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Inisialisasi Select2
                $('#select-karyawan').select2({
                    placeholder: 'Ketik untuk mencari nama karyawan...',
                    width: '100%',
                    // Ambil data karyawan dari variabel PHP yang dikirim controller
                    data: [
                        @foreach ($employees as $employee)
                            {
                                id: '{{ $employee->id }}',
                                text: '{{ $employee->nama }}'
                            },
                        @endforeach
                    ]
                });

                // Inisialisasi Flatpickr
                const dateContainer = document.getElementById('tanggal-insentif-container');
                flatpickr("#tanggal-insentif-display", {
                    mode: "multiple",
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        dateContainer.innerHTML = '';
                        selectedDates.forEach(date => {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'tanggal_insentif[]';
                            hiddenInput.value = instance.formatDate(date, "Y-m-d");
                            dateContainer.appendChild(hiddenInput);
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
