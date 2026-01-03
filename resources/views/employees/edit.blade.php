<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Data {{ ucfirst($employee->tipe_karyawan) }}: {{ $employee->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Input Hidden untuk Tipe Karyawan (Agar validasi controller lolos) --}}
                        <input type="hidden" name="tipe_karyawan" value="{{ $employee->tipe_karyawan }}">

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

                            <div x-show="tab === 'utama'" class="space-y-4">
                                {{-- INFO READONLY TIPE KARYAWAN --}}
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-info-circle text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                Anda sedang mengedit data
                                                <strong>{{ ucfirst($employee->tipe_karyawan) }}</strong>.
                                                Tipe karyawan tidak dapat diubah dari menu edit.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Kolom Kiri --}}
                                    <div class="space-y-4">
                                        {{-- KHUSUS DOSEN --}}
                                        @if ($employee->tipe_karyawan === 'dosen')
                                            <div>
                                                <x-input-label for="gelar_depan" value="Gelar Depan" />
                                                <x-text-input id="gelar_depan" class="block mt-1 w-full" type="text"
                                                    name="gelar_depan" :value="old('gelar_depan', $employee->gelar_depan)" />
                                            </div>
                                        @endif

                                        <div>
                                            <x-input-label for="nama" value="Nama Lengkap" />
                                            <x-text-input id="nama" class="block mt-1 w-full" type="text"
                                                name="nama" :value="old('nama', $employee->nama)" required />
                                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                                        </div>

                                        {{-- KHUSUS DOSEN --}}
                                        @if ($employee->tipe_karyawan === 'dosen')
                                            <div>
                                                <x-input-label for="nidn" value="NIDN" />
                                                <x-text-input id="nidn" class="block mt-1 w-full" type="text"
                                                    name="nidn" :value="old('nidn', $employee->nidn)" />
                                            </div>
                                        @endif

                                        <div>
                                            <x-input-label for="jabatan" value="Jabatan" />
                                            <x-text-input id="jabatan" class="block mt-1 w-full" type="text"
                                                name="jabatan" :value="old('jabatan', $employee->jabatan)" required />
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan --}}
                                    <div class="space-y-4">
                                        {{-- KHUSUS DOSEN --}}
                                        @if ($employee->tipe_karyawan === 'dosen')
                                            <div>
                                                <x-input-label for="gelar_belakang" value="Gelar Belakang" />
                                                <x-text-input id="gelar_belakang" class="block mt-1 w-full"
                                                    type="text" name="gelar_belakang" :value="old('gelar_belakang', $employee->gelar_belakang)" />
                                            </div>

                                            <div>
                                                <x-input-label for="status_dosen" value="Status Kepegawaian Dosen" />
                                                <select name="status_dosen" id="status_dosen"
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="tetap"
                                                        {{ old('status_dosen', $employee->status_dosen) == 'tetap' ? 'selected' : '' }}>
                                                        Dosen Tetap</option>
                                                    <option value="honorer"
                                                        {{ old('status_dosen', $employee->status_dosen) == 'honorer' ? 'selected' : '' }}>
                                                        Dosen Honorer / LB</option>
                                                    <option value="luar_biasa"
                                                        {{ old('status_dosen', $employee->status_dosen) == 'luar_biasa' ? 'selected' : '' }}>
                                                        Dosen Luar Biasa</option>
                                                </select>
                                            </div>
                                        @endif

                                        <div class="@if ($employee->tipe_karyawan !== 'dosen') mt-0 @endif">
                                            <x-input-label for="departemen" value="Departemen" />
                                            <x-text-input id="departemen" class="block mt-1 w-full" type="text"
                                                name="departemen" :value="old('departemen', $employee->departemen)" required />
                                        </div>

                                        {{-- List Matkul yang sedang diajar (Read Only) --}}
                                        @if ($employee->tipe_karyawan === 'dosen')
                                            <div class="bg-gray-50 p-3 rounded-md border border-gray-200 mt-4">
                                                <p class="text-sm font-semibold mb-2">Enrollment Aktif:</p>
                                                @if ($enrollments->count() > 0)
                                                    <ul class="list-disc list-inside text-sm text-gray-600">
                                                        @foreach ($enrollments as $enr)
                                                            <li>{{ $enr->matkul->nama_matkul }} (Kelas
                                                                {{ $enr->kelas ?? '-' }})</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-xs text-gray-500 italic">Belum ada enrollment aktif.
                                                    </p>
                                                @endif
                                                <a href="{{ route('enrollments.index') }}"
                                                    class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Kelola
                                                    Enrollment &rarr;</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <x-input-label for="gaji_pokok" value="Gaji Pokok" />
                                        <x-text-input id="gaji_pokok" class="block mt-1 w-full" type="number"
                                            name="gaji_pokok" :value="old('gaji_pokok', $employee->gaji_pokok)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="transport" value="Transport" />
                                        <x-text-input id="transport" class="block mt-1 w-full" type="number"
                                            name="transport" :value="old('transport', $employee->transport)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="tunjangan" value="Tunjangan" />
                                        <x-text-input id="tunjangan" class="block mt-1 w-full" type="number"
                                            name="tunjangan" :value="old('tunjangan', $employee->tunjangan)" required />
                                    </div>
                                </div>
                            </div>

                            {{-- TAB DETAIL (SAMA PERSIS DENGAN SEBELUMNYA) --}}
                            <div x-show="tab === 'detail'" style="display: none;">
                                {{-- Isi form detail sama seperti yang di file create,
                                     hanya value-nya mengambil dari $employee->detail --}}
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="tanggal_masuk" value="Tanggal Masuk" />
                                            <x-text-input id="tanggal_masuk" class="block mt-1 w-full" type="date"
                                                name="tanggal_masuk" :value="old('tanggal_masuk', $employee->detail->tanggal_masuk ?? '')" />
                                        </div>
                                        <div>
                                            <x-input-label for="no_hp" value="Nomor HP" />
                                            <x-text-input id="no_hp" class="block mt-1 w-full" type="text"
                                                name="no_hp" :value="old('no_hp', $employee->detail->no_hp ?? '')" />
                                        </div>
                                    </div>

                                    {{-- Sisanya sama dengan struktur create, hanya populate value --}}
                                    {{-- ... (lanjutkan field detail lainnya: status_pernikahan, pendidikan, alamat, foto) ... --}}

                                    <div class="mt-4">
                                        <x-input-label for="alamat" value="Alamat Lengkap" />
                                        <textarea name="alamat" id="alamat" rows="3"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('alamat', $employee->detail->alamat ?? '') }}</textarea>
                                    </div>

                                    <div class="mt-4">
                                        <x-input-label for="foto" value="Foto Profil" />
                                        @if ($employee->detail?->foto)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $employee->detail->foto) }}"
                                                    alt="Foto saat ini"
                                                    class="w-20 h-20 rounded-md object-cover border">
                                            </div>
                                        @endif
                                        <input type="file" name="foto" id="foto"
                                            class="block w-full text-sm mt-1 border border-gray-300 rounded-lg bg-gray-50">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('employees.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update Data</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
