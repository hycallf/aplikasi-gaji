<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Enrollment Dosen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('enrollments.update', $enrollment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="employee_id" value="Dosen" />
                                <select name="employee_id" id="employee_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}"
                                            {{ old('employee_id', $enrollment->employee_id) == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="matkul_id" value="Mata Kuliah" />
                                <select name="matkul_id" id="matkul_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    @foreach ($matkuls as $matkul)
                                        <option value="{{ $matkul->id }}"
                                            {{ old('matkul_id', $enrollment->matkul_id) == $matkul->id ? 'selected' : '' }}>
                                            {{ $matkul->nama_matkul }} ({{ $matkul->sks }} SKS)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('matkul_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="academic_year_id" value="Tahun Ajaran" />
                                <select name="academic_year_id" id="academic_year_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}"
                                            {{ old('academic_year_id', $enrollment->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="kelas" value="Kelas" />
                                    <x-text-input id="kelas" class="block mt-1 w-full" type="text" name="kelas"
                                        :value="old('kelas', $enrollment->kelas)" />
                                    <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="jumlah_mahasiswa" value="Jumlah Mahasiswa" />
                                    <x-text-input id="jumlah_mahasiswa" class="block mt-1 w-full" type="number"
                                        name="jumlah_mahasiswa" :value="old('jumlah_mahasiswa', $enrollment->jumlah_mahasiswa)" min="0" />
                                    <x-input-error :messages="$errors->get('jumlah_mahasiswa')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="catatan" value="Catatan" />
                                <textarea name="catatan" id="catatan" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('catatan', $enrollment->catatan) }}</textarea>
                                <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('enrollments.index')">Kembali</x-secondary-button>
                            <x-primary-button>Update Enrollment</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
