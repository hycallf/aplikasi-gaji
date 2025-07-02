<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Data Karyawan: {{ $employee->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{ tipeKaryawan: '{{ old('tipe_karyawan', $employee->tipe_karyawan) }}' }">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                        :value="old('nama', $employee->nama)" required />
                                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="jabatan" value="Jabatan" />
                                    <x-text-input id="jabatan" class="block mt-1 w-full" type="text" name="jabatan"
                                        :value="old('jabatan', $employee->jabatan)" required />
                                    <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="departemen" value="Departemen" />
                                    <x-text-input id="departemen" class="block mt-1 w-full" type="text"
                                        name="departemen" :value="old('departemen', $employee->departemen)" required />
                                    <x-input-error :messages="$errors->get('departemen')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="tipe_karyawan" value="Tipe Karyawan" />
                                    <select name="tipe_karyawan" id="tipe_karyawan"
                                        @change="tipeKaryawan = $event.target.value"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                        <option value="karyawan"
                                            {{ old('tipe_karyawan', $employee->tipe_karyawan) == 'karyawan' ? 'selected' : '' }}>
                                            Karyawan</option>
                                        <option value="dosen"
                                            {{ old('tipe_karyawan', $employee->tipe_karyawan) == 'dosen' ? 'selected' : '' }}>
                                            Dosen</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('tipe_karyawan')" class="mt-2" />
                                </div>
                                <div x-show="tipeKaryawan === 'dosen'" style="display: none;" class="mt-4">
                                    <x-input-label for="matkuls" value="Mata Kuliah yang Diajar" />
                                    <select name="matkuls[]" id="select-matkul" class="block w-full mt-1"
                                        multiple="multiple">
                                        @foreach ($matkuls as $matkul)
                                            {{-- Cek apakah dosen ini sudah mengajar matkul ini --}}
                                            <option value="{{ $matkul->id }}"
                                                {{ in_array($matkul->id, $employee->matkuls->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $matkul->nama_matkul }} ({{ $matkul->sks }} SKS)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="gaji_pokok" value="Gaji Pokok" />
                                    <x-text-input id="gaji_pokok" class="block mt-1 w-full" type="number"
                                        name="gaji_pokok" :value="old('gaji_pokok', $employee->gaji_pokok)" required />
                                    <x-input-error :messages="$errors->get('gaji_pokok')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="transport" value="Uang Transport Harian" />
                                    <x-text-input id="transport" class="block mt-1 w-full" type="number"
                                        name="transport" :value="old('transport', $employee->transport)" required />
                                    <x-input-error :messages="$errors->get('transport')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="tunjangan" value="Tunjangan" />
                                    <x-text-input id="tunjangan" class="block mt-1 w-full" type="number"
                                        name="tunjangan" :value="old('tunjangan', $employee->tunjangan)" required />
                                    <x-input-error :messages="$errors->get('tunjangan')" class="mt-2" />
                                </div>
                            </div>

                            <div x-show="tab === 'detail'" style="display: none;">
                                <h3 class="text-lg font-bold mb-4">Data Personal</h3>
                                <div>
                                    <x-input-label for="tanggal_masuk" value="Tanggal Masuk Kerja" />
                                    <x-text-input id="tanggal_masuk" class="block mt-1 w-full" type="date"
                                        name="tanggal_masuk" :value="old('tanggal_masuk', $employee->detail->tanggal_masuk ?? '')" />
                                    <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="no_hp" value="Nomor HP" />
                                    <x-text-input id="no_hp" class="block mt-1 w-full" type="text"
                                        name="no_hp" :value="old('no_hp', $employee->detail->no_hp ?? '')" />
                                    <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="status_pernikahan" value="Status Pernikahan" />
                                    <select name="status_pernikahan" id="status_pernikahan"
                                        class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                        <option value="Lajang"
                                            {{ old('status_pernikahan', $employee->detail->status_pernikahan ?? '') == 'Lajang' ? 'selected' : '' }}>
                                            Lajang</option>
                                        <option value="Menikah"
                                            {{ old('status_pernikahan', $employee->detail->status_pernikahan ?? '') == 'Menikah' ? 'selected' : '' }}>
                                            Menikah</option>
                                        <option value="Cerai"
                                            {{ old('status_pernikahan', $employee->detail->status_pernikahan ?? '') == 'Cerai' ? 'selected' : '' }}>
                                            Cerai</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status_pernikahan')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="jumlah_anak" value="Jumlah Anak" />
                                    <x-text-input id="jumlah_anak" class="block mt-1 w-full" type="number"
                                        name="jumlah_anak" :value="old('jumlah_anak', $employee->detail->jumlah_anak ?? 0)" />
                                    <x-input-error :messages="$errors->get('jumlah_anak')" class="mt-2" />
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="pendidikan_terakhir" value="Pendidikan Terakhir" />
                                        <select name="pendidikan_terakhir" id="pendidikan_terakhir"
                                            class="block mt-1 w-full border-gray-300 rounded-md">
                                            @php $pendidikan = optional($employee->detail)->pendidikan_terakhir; @endphp
                                            <option value="">-- Pilih --</option>
                                            <option value="SMA/SMK Sederajat" @selected($pendidikan == 'SMA/SMK Sederajat')>SMA/SMK
                                                Sederajat</option>
                                            <option value="D3" @selected($pendidikan == 'D3')>D3</option>
                                            <option value="S1" @selected($pendidikan == 'S1')>S1</option>
                                            <option value="S2" @selected($pendidikan == 'S2')>S2</option>
                                            <option value="S3" @selected($pendidikan == 'S3')>S3</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('pendidikan_terakhir')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="jurusan" value="Jurusan" />
                                        <x-text-input id="jurusan" class="block mt-1 w-full" type="text"
                                            name="jurusan" :value="old('jurusan', optional($employee->detail)->jurusan)" />
                                        <x-input-error :messages="$errors->get('jurusan')" class="mt-2" />
                                    </div>
                                </div>

                                {{-- Domisili --}}
                                <div class="mt-4">
                                    <x-input-label for="domisili" value="Domisili (Kota)" />
                                    <x-text-input id="domisili" class="block mt-1 w-full" type="text"
                                        name="domisili" :value="old('domisili', optional($employee->detail)->domisili)" placeholder="Contoh: Jakarta Pusat" />
                                    <x-input-error :messages="$errors->get('domisili')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="alamat" value="Alamat Lengkap" />
                                    <textarea name="alamat" id="alamat" rows="3"
                                        class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('alamat', $employee->detail->alamat ?? '') }}</textarea>
                                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="foto"
                                        value="Foto Karyawan (Kosongkan jika tidak diubah)" />
                                    <input type="file" name="foto" id="foto"
                                        class="block w-full text-sm ... mt-1">
                                    @if ($employee->detail?->foto)
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Foto Saat Ini:</p>
                                            <img src="{{ asset('storage/' . $employee->detail->foto) }}"
                                                alt="Foto saat ini" class="w-24 h-24 rounded-md object-cover">
                                        </div>
                                    @endif
                                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('employees.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update Karyawan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Inisialisasi Select2 untuk dropdown matkul --}}
        <script>
            $(document).ready(function() {
                $('#select-matkul').select2({
                    placeholder: 'Pilih satu atau lebih mata kuliah',
                    width: '100%'
                });
            });
        </script>
    @endpush
</x-app-layout>
