<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Event Baru</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('events.store') }}">
                        @csrf
                        {{-- Nama Event (sudah full-width) --}}
                        <div>
                            <x-input-label for="nama_event" value="Nama Event" />
                            <x-text-input id="nama_event" class="block mt-1 w-full" type="text" name="nama_event"
                                required />
                        </div>

                        {{-- Input Tanggal (tetap 2 kolom) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="start_date" value="Tanggal Mulai" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date"
                                    required />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Tanggal Selesai (Opsional)" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" />
                            </div>
                        </div>

                        {{-- Deskripsi (sudah full-width) --}}
                        <div class="mt-4">
                            <x-input-label for="deskripsi" value="Deskripsi" />
                            <textarea name="deskripsi" id="deskripsi" rows="4"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('events.index')">Kembali</x-secondary-button>
                            <x-primary-button>Simpan Event</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
