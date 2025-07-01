<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pengaturan Profil Perusahaan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('company.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Nama Perusahaan --}}
                        <div>
                            <x-input-label for="nama_perusahaan" value="Nama Perusahaan" />
                            <x-text-input id="nama_perusahaan" class="block mt-1 w-full" type="text"
                                name="nama_perusahaan" :value="old('nama_perusahaan', $profile->nama_perusahaan)" required />
                            <x-input-error :messages="$errors->get('nama_perusahaan')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="nama_perwakilan" value="Nama Perwakilan (untuk TTD)" />
                            <x-text-input id="nama_perwakilan" class="block mt-1 w-full" type="text"
                                name="nama_perwakilan" :value="old('nama_perwakilan', $profile->nama_perwakilan)" />
                            <x-input-error :messages="$errors->get('nama_perwakilan')" class="mt-2" />
                        </div>
                        {{-- Email Kontak --}}
                        <div class="mt-4">
                            <x-input-label for="email_kontak" value="Email Kontak" />
                            <x-text-input id="email_kontak" class="block mt-1 w-full" type="email" name="email_kontak"
                                :value="old('email_kontak', $profile->email_kontak)" />
                            <x-input-error :messages="$errors->get('email_kontak')" class="mt-2" />
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="mt-4">
                            <x-input-label for="no_telepon" value="Nomor Telepon" />
                            <x-text-input id="no_telepon" class="block mt-1 w-full" type="text" name="no_telepon"
                                :value="old('no_telepon', $profile->no_telepon)" />
                            <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
                        </div>

                        {{-- Alamat --}}
                        <div class="mt-4">
                            <x-input-label for="alamat" value="Alamat" />
                            <textarea name="alamat" id="alamat" rows="3"
                                class="block w-full mt-1 border-gray-300 dark:border-gray-700 rounded-md shadow-sm">{{ old('alamat', $profile->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        {{-- Logo --}}
                        <div class="mt-4">
                            <x-input-label for="logo" value="Logo Perusahaan (Kosongkan jika tidak diubah)" />
                            <input type="file" name="logo" id="logo"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 mt-1">
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                            @if ($profile->logo)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Logo Saat Ini:</p>
                                    <img src="{{ asset('storage/' . $profile->logo) }}" alt="Logo saat ini"
                                        class="w-32 h-auto rounded-md mt-1">
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6 border-t pt-6">
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
