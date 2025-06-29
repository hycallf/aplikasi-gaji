<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tambah Karyawan Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Alpine.js component untuk TABS --}}
                        <div x-data="{ tab: 'utama' }">
                            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    <a href="#" @click.prevent="tab = 'utama'"
                                        :class="{ 'border-indigo-500 text-indigo-600': tab === 'utama', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'utama' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                        Data Utama
                                    </a>
                                    <a href="#" @click.prevent="tab = 'detail'"
                                        :class="{ 'border-indigo-500 text-indigo-600': tab === 'detail', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'detail' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                        Data Detail
                                    </a>
                                </nav>
                            </div>

                            <div x-show="tab === 'utama'">
                                <h3 class="text-lg font-bold mb-4">Data Pekerjaan & Gaji</h3>
                                <div>
                                    <x-input-label for="nama" value="Nama Lengkap" />
                                    <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama"
                                        :value="old('nama')" required />
                                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="jabatan" value="Jabatan" />
                                    <x-text-input id="jabatan" class="block mt-1 w-full" type="text" name="jabatan"
                                        :value="old('jabatan')" required />
                                    <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="tipe_karyawan" value="Tipe Karyawan" />
                                    <select name="tipe_karyawan" id="tipe_karyawan"
                                        class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                        <option value="karyawan"
                                            {{ old('tipe_karyawan') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                        <option value="dosen" {{ old('tipe_karyawan') == 'dosen' ? 'selected' : '' }}>
                                            Dosen</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('tipe_karyawan')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="gaji_pokok" value="Gaji Pokok" />
                                    <x-text-input id="gaji_pokok" class="block mt-1 w-full" type="number"
                                        name="gaji_pokok" :value="old('gaji_pokok')" required />
                                    <x-input-error :messages="$errors->get('gaji_pokok')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="transport" value="Uang Transport Harian" />
                                    <x-text-input id="transport" class="block mt-1 w-full" type="number"
                                        name="transport" :value="old('transport')" required />
                                    <x-input-error :messages="$errors->get('transport')" class="mt-2" />
                                </div>
                            </div>

                            <div x-show="tab === 'detail'" style="display: none;">
                                <h3 class="text-lg font-bold mb-4">Data Personal</h3>
                                <div>
                                    <x-input-label for="tanggal_masuk" value="Tanggal Masuk Kerja" />
                                    <x-text-input id="tanggal_masuk" class="block mt-1 w-full" type="date"
                                        name="tanggal_masuk" :value="old('tanggal_masuk')" required />
                                    <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="no_hp" value="Nomor HP" />
                                    <x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp"
                                        :value="old('no_hp')" />
                                    <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="status_pernikahan" value="Status Pernikahan" />
                                    <select name="status_pernikahan" id="status_pernikahan"
                                        class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                        <option value="Lajang"
                                            {{ old('status_pernikahan') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
                                        <option value="Menikah"
                                            {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah
                                        </option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status_pernikahan')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="jumlah_anak" value="Jumlah Anak" />
                                    <x-text-input id="jumlah_anak" class="block mt-1 w-full" type="number"
                                        name="jumlah_anak" :value="old('jumlah_anak', 0)" />
                                    <x-input-error :messages="$errors->get('jumlah_anak')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="pendidikan_terakhir" value="Pendidikan Terakhir" />
                                        <select name="pendidikan_terakhir" id="pendidikan_terakhir"
                                            class="block mt-1 w-full border-gray-300 rounded-md">
                                            <option value="">-- Pilih --</option>
                                            <option value="SMA/SMK Sederajat"
                                                {{ old('pendidikan_terakhir') == 'SMA/SMK Sederajat' ? 'selected' : '' }}>
                                                SMA/SMK
                                                Sederajat</option>
                                            <option value="D3"
                                                {{ old('pendidikan_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="S1"
                                                {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2"
                                                {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3"
                                                {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('pendidikan_terakhir')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="jurusan" value="Jurusan" />
                                        <x-text-input id="jurusan" class="block mt-1 w-full" type="text"
                                            name="jurusan" :value="old('jurusan')" />
                                        <x-input-error :messages="$errors->get('jurusan')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="domisili" value="Domisili (kota)" />
                                    <x-text-input id="domisili" class="block mt-1 w-full" type="text"
                                        name="domisili" :value="old('domisili')" />
                                    <x-input-error :messages="$errors->get('domisili')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="alamat" value="Alamat KTP" />
                                    <textarea name="alamat" id="alamat" rows="3"
                                        class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat') }}</textarea>
                                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="foto" value="Foto Karyawan (Opsional)" />
                                    <input type="file" name="foto" id="foto"
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-700 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mt-1">
                                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('employees.index')">Kembali</x-secondary-button>
                            <x-primary-button>Simpan Karyawan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
