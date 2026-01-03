<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Enrollment Dosen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('enrollments.store') }}">
                        @csrf

                        <div class="space-y-6">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            Data ini akan ditambahkan ke Tahun Ajaran Aktif:
                                            <span class="font-bold">{{ $academicYear->nama_tahun_ajaran }}
                                                ({{ ucfirst($academicYear->semester) }})</span>
                                        </p>
                                        {{-- Hidden Input untuk ID Tahun Ajaran --}}
                                        <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="employee_id" value="Nama Dosen" />
                                <select name="employee_id" id="employee_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}"
                                            {{ old('employee_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->nama_lengkap ?? $dosen->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="matkul_id" value="Mata Kuliah" />
                                <select name="matkul_id" id="matkul_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach ($matkuls as $matkul)
                                        <option value="{{ $matkul->id }}"
                                            {{ old('matkul_id') == $matkul->id ? 'selected' : '' }}>
                                            {{ $matkul->nama_matkul }} ({{ $matkul->sks }} SKS)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('matkul_id')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="kelas" value="Nama Kelas" />
                                    <x-text-input id="kelas" class="block mt-1 w-full" type="text" name="kelas"
                                        :value="old('kelas')" placeholder="Contoh: A, B, Reguler Pagi" />
                                    <p class="mt-1 text-sm text-gray-500">Opsional. Kosongkan jika hanya ada 1 kelas.
                                    </p>
                                    <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="jumlah_mahasiswa" value="Estimasi Jumlah Mahasiswa" />
                                    <x-text-input id="jumlah_mahasiswa" class="block mt-1 w-full" type="number"
                                        name="jumlah_mahasiswa" :value="old('jumlah_mahasiswa', 0)" min="0" />
                                    <x-input-error :messages="$errors->get('jumlah_mahasiswa')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="catatan" value="Catatan Tambahan" />
                                <textarea name="catatan" id="catatan" rows="3"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('catatan') }}</textarea>
                                <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <x-secondary-button :href="route('enrollments.index')">
                                {{ __('Batal') }}
                            </x-secondary-button>

                            <x-primary-button>
                                {{ __('Simpan Enrollment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
