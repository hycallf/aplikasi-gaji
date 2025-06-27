<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Event: {{ $event->nama_event }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('events.update', $event->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama Event (sudah full-width) --}}
                        <div>
                            <x-input-label for="nama_event" value="Nama Event" />
                            <x-text-input id="nama_event" class="block mt-1 w-full" type="text" name="nama_event"
                                :value="old('nama_event', $event->nama_event)" required />
                            <x-input-error :messages="$errors->get('nama_event')" class="mt-2" />
                        </div>

                        {{-- Input Tanggal (tetap 2 kolom) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="start_date" value="Tanggal Mulai" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date"
                                    :value="old('start_date', $event->start_date)" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Tanggal Selesai (Opsional)" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date"
                                    :value="old('end_date', $event->end_date)" />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Deskripsi (sudah full-width) --}}
                        <div class="mt-4">
                            <x-input-label for="deskripsi" value="Deskripsi" />
                            <textarea name="deskripsi" id="deskripsi" rows="4"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('events.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update Event</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
