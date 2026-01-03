<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Judul Dinamis --}}
            Tambah {{ ucfirst($tipe) }} Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Hidden Input untuk Tipe Karyawan (Otomatis terisi sesuai URL) --}}
                        <input type="hidden" name="tipe_karyawan" value="{{ $tipe }}">

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

                            {{-- TAB 1: DATA UTAMA --}}
                            <div x-show="tab === 'utama'" class="space-y-4">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Informasi Pekerjaan</h3>

                                {{-- Layout Grid 2 Kolom --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    {{-- Kolom Kiri --}}
                                    <div class="space-y-4">
                                        {{-- KHUSUS DOSEN: Gelar Depan --}}
                                        @if ($tipe === 'dosen')
                                            <div>
                                                <x-input-label for="gelar_depan" value="Gelar Depan" />
                                                <x-text-input id="gelar_depan" class="block mt-1 w-full" type="text"
                                                    name="gelar_depan" :value="old('gelar_depan')"
                                                    placeholder="Contoh: Dr., Ir." />
                                                <x-input-error :messages="$errors->get('gelar_depan')" class="mt-2" />
                                            </div>
                                        @endif

                                        <div>
                                            <x-input-label for="nama" value="Nama Lengkap (Tanpa Gelar)" />
                                            <x-text-input id="nama" class="block mt-1 w-full" type="text"
                                                name="nama" :value="old('nama')" required
                                                placeholder="Nama asli sesuai KTP" />
                                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                                        </div>

                                        {{-- KHUSUS DOSEN: NIDN --}}
                                        @if ($tipe === 'dosen')
                                            <div>
                                                <x-input-label for="nidn"
                                                    value="NIDN (Nomor Induk Dosen Nasional)" />
                                                <x-text-input id="nidn" class="block mt-1 w-full" type="text"
                                                    name="nidn" :value="old('nidn')" />
                                                <x-input-error :messages="$errors->get('nidn')" class="mt-2" />
                                            </div>
                                        @endif

                                        <div>
                                            <x-input-label for="jabatan" value="Jabatan Struktural" />
                                            <x-text-input id="jabatan" class="block mt-1 w-full" type="text"
                                                name="jabatan" :value="old('jabatan')" required
                                                placeholder="Contoh: Staff IT, Kaprodi, Dekan" />
                                            <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan --}}
                                    <div class="space-y-4">
                                        {{-- KHUSUS DOSEN: Gelar Belakang --}}
                                        @if ($tipe === 'dosen')
                                            <div>
                                                <x-input-label for="gelar_belakang" value="Gelar Belakang" />
                                                <x-text-input id="gelar_belakang" class="block mt-1 w-full"
                                                    type="text" name="gelar_belakang" :value="old('gelar_belakang')"
                                                    placeholder="Contoh: S.Kom, M.T, Ph.D" />
                                                <x-input-error :messages="$errors->get('gelar_belakang')" class="mt-2" />
                                            </div>
                                        @endif

                                        {{-- KHUSUS DOSEN: Status Dosen --}}
                                        @if ($tipe === 'dosen')
                                            <div>
                                                <x-input-label for="status_dosen" value="Status Kepegawaian Dosen" />
                                                <select name="status_dosen" id="status_dosen"
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="tetap"
                                                        {{ old('status_dosen') == 'tetap' ? 'selected' : '' }}>Dosen
                                                        Tetap</option>
                                                    <option value="honorer"
                                                        {{ old('status_dosen') == 'honorer' ? 'selected' : '' }}>Dosen
                                                        Honorer / LB</option>
                                                    <option value="luar_biasa"
                                                        {{ old('status_dosen') == 'luar_biasa' ? 'selected' : '' }}>
                                                        Dosen Luar Biasa</option>
                                                </select>
                                                <x-input-error :messages="$errors->get('status_dosen')" class="mt-2" />
                                            </div>
                                        @else
                                            {{-- SPACING FILLER UNTUK KARYAWAN AGAR RAPI --}}
                                            <div class="hidden md:block h-[4.5rem]"></div>
                                        @endif

                                        <div>
                                            <x-input-label for="departemen" value="Departemen / Unit Kerja" />
                                            <x-text-input id="departemen" class="block mt-1 w-full" type="text"
                                                name="departemen" :value="old('departemen')" required />
                                            <x-input-error :messages="$errors->get('departemen')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-lg font-bold mt-8 mb-4 border-b pb-2">Informasi Gaji & Tunjangan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="gaji_pokok" value="Gaji Pokok (Rp)" />
                                        <x-text-input id="gaji_pokok" class="block mt-1 w-full" type="number"
                                            name="gaji_pokok" :value="old('gaji_pokok')" required />
                                        <x-input-error :messages="$errors->get('gaji_pokok')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="transport" value="Transport Harian (Rp)" />
                                        <x-text-input id="transport" class="block mt-1 w-full" type="number"
                                            name="transport" :value="old('transport')" required />
                                        <x-input-error :messages="$errors->get('transport')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="tunjangan" value="Tunjangan Tetap (Rp)" />
                                        <x-text-input id="tunjangan" class="block mt-1 w-full" type="number"
                                            name="tunjangan" :value="old('tunjangan')" required />
                                        <x-input-error :messages="$errors->get('tunjangan')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: DATA DETAIL (Sama seperti sebelumnya, tidak perlu diubah logicnya) --}}
                            <div x-show="tab === 'detail'" style="display: none;" class="space-y-4">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Data Personal</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="tanggal_masuk" value="Tanggal Masuk Kerja" />
                                        <x-text-input id="tanggal_masuk" class="block mt-1 w-full" type="date"
                                            name="tanggal_masuk" :value="old('tanggal_masuk')" />
                                        <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="no_hp" value="Nomor HP / WhatsApp" />
                                        <x-text-input id="no_hp" class="block mt-1 w-full" type="text"
                                            name="no_hp" :value="old('no_hp')" />
                                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="status_pernikahan" value="Status Pernikahan" />
                                        <select name="status_pernikahan" id="status_pernikahan"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                            <option value="Lajang"
                                                {{ old('status_pernikahan') == 'Lajang' ? 'selected' : '' }}>Lajang
                                            </option>
                                            <option value="Menikah"
                                                {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah
                                            </option>
                                            <option value="Cerai"
                                                {{ old('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>Cerai
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="jumlah_anak" value="Jumlah Anak" />
                                        <x-text-input id="jumlah_anak" class="block mt-1 w-full" type="number"
                                            name="jumlah_anak" :value="old('jumlah_anak', 0)" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="pendidikan_terakhir" value="Pendidikan Terakhir" />
                                        <select name="pendidikan_terakhir" id="pendidikan_terakhir"
                                            class="block mt-1 w-full border-gray-300 rounded-md">
                                            <option value="">-- Pilih --</option>
                                            <option value="SMA/SMK Sederajat"
                                                {{ old('pendidikan_terakhir') == 'SMA/SMK Sederajat' ? 'selected' : '' }}>
                                                SMA/SMK Sederajat</option>
                                            <option value="D3"
                                                {{ old('pendidikan_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="S1"
                                                {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2"
                                                {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3"
                                                {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="jurusan" value="Jurusan Pendidikan" />
                                        <x-text-input id="jurusan" class="block mt-1 w-full" type="text"
                                            name="jurusan" :value="old('jurusan')" />
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="domisili" value="Domisili (Kota Tempat Tinggal)" />
                                    <x-text-input id="domisili" class="block mt-1 w-full" type="text"
                                        name="domisili" :value="old('domisili')" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="alamat" value="Alamat Lengkap (Sesuai KTP)" />
                                    <textarea name="alamat" id="alamat" rows="3"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('alamat') }}</textarea>
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="foto" value="Foto Profil" />
                                    <input type="file" name="foto" id="foto"
                                        class="block w-full mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
                                    <p class="mt-1 text-sm text-gray-500">JPG, PNG, atau WEBP. Maks 2MB.</p>
                                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-between mt-8 border-t pt-6">
                            <x-secondary-button :href="route('employees.index')">
                                Batal
                            </x-secondary-button>
                            <x-primary-button>
                                Simpan {{ ucfirst($tipe) }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
