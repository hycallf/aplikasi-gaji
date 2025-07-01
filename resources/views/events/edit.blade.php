<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Jenis Event</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('events.update', $event->id) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="nama_event" value="Nama Jenis Event/Insentif" />
                            <x-text-input id="nama_event" class="block mt-1 w-full" type="text" name="nama_event"
                                :value="old('nama_event', $event->nama_event)" required />
                            <x-input-error :messages="$errors->get('nama_event')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('events.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update Jenis Event</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
