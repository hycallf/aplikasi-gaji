<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($academicYear) ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST"
                        action="{{ isset($academicYear) ? route('academic-years.update', $academicYear->id) : route('academic-years.store') }}">
                        @csrf
                        @if (isset($academicYear))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nama_tahun_ajaran" value="Nama Tahun Ajaran" />
                                <x-text-input id="nama_tahun_ajaran" class="block mt-1 w-full" type="text"
                                    name="nama_tahun_ajaran" :value="old('nama_tahun_ajaran', $academicYear->nama_tahun_ajaran ?? '')" placeholder="Contoh: 2024/2025"
                                    required />
                                <x-input-error :messages="$errors->get('nama_tahun_ajaran')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="semester" value="Semester" />
                                <select name="semester" id="semester"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                                    <option value="ganjil"
                                        {{ old('semester', $academicYear->semester ?? '') == 'ganjil' ? 'selected' : '' }}>
                                        Ganjil</option>
                                    <option value="genap"
                                        {{ old('semester', $academicYear->semester ?? '') == 'genap' ? 'selected' : '' }}>
                                        Genap</option>
                                </select>
                                <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                                <x-text-input id="tanggal_mulai" class="block mt-1 w-full" type="date"
                                    name="tanggal_mulai" :value="old('tanggal_mulai', $academicYear->tanggal_mulai ?? '')" required />
                                <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_selesai" value="Tanggal Selesai" />
                                <x-text-input id="tanggal_selesai" class="block mt-1 w-full" type="date"
                                    name="tanggal_selesai" :value="old('tanggal_selesai', $academicYear->tanggal_selesai ?? '')" required />
                                <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $academicYear->is_active ?? false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Aktifkan tahun ajaran ini</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">* Mengaktifkan tahun ajaran ini akan menonaktifkan
                                tahun ajaran lainnya</p>
                        </div>

                        <div class="flex items-center justify-between mt-6 border-t pt-6">
                            <x-secondary-button :href="route('academic-years.index')">Kembali</x-secondary-button>
                            <x-primary-button>{{ isset($academicYear) ? 'Update' : 'Simpan' }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
