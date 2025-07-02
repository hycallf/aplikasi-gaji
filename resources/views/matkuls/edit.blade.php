<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Mata Kuliah</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('matkuls.update', $matkul->id) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="nama_matkul" value="Nama Mata Kuliah" />
                            <x-text-input id="nama_matkul" class="block mt-1 w-full" type="text" name="nama_matkul"
                                :value="old('nama_matkul', $matkul->nama_matkul)" required autofocus />
                            <x-input-error :messages="$errors->get('nama_matkul')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="sks" value="Jumlah SKS" />
                            <x-text-input id="sks" class="block mt-1 w-full" type="number" name="sks"
                                :value="old('sks', $matkul->sks)" required />
                            <x-input-error :messages="$errors->get('sks')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('matkuls.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
