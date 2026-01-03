<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fa-solid fa-cog mr-2"></i>Pengaturan Sistem
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('settings.update') }}" x-data="settingsForm()">
                        @csrf
                        @method('PUT')

                        @foreach ($settings as $group => $groupSettings)
                            <div class="mb-8 pb-8 border-b last:border-b-0">
                                <h3 class="text-lg font-bold mb-4 text-indigo-600 capitalize">
                                    <i
                                        class="fa-solid fa-{{ $group === 'attendance' ? 'calendar-check' : ($group === 'payroll' ? 'money-bill' : 'cog') }} mr-2"></i>
                                    {{ str_replace('_', ' ', $group) }}
                                </h3>

                                @foreach ($groupSettings as $setting)
                                    <div class="mb-6">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <label class="block font-semibold text-gray-700">
                                                    {{ $setting->label }}
                                                </label>
                                                @if ($setting->description)
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        <i class="fa-solid fa-info-circle mr-1"></i>
                                                        {{ $setting->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($setting->key === 'work_days')
                                            {{-- Special toggle untuk work_days --}}
                                            <div class="grid grid-cols-7 gap-2 mt-3">
                                                @php
                                                    $days = [
                                                        'Minggu',
                                                        'Senin',
                                                        'Selasa',
                                                        'Rabu',
                                                        'Kamis',
                                                        'Jumat',
                                                        'Sabtu',
                                                    ];
                                                    $dayIcons = ['🌙', '💼', '💼', '💼', '💼', '💼', '🎉'];
                                                @endphp
                                                @foreach ($days as $index => $day)
                                                    <div>
                                                        <button type="button"
                                                            @click="toggleWorkDay({{ $index }})"
                                                            class="w-full p-3 border-2 rounded-lg transition-all duration-200 transform hover:scale-105"
                                                            :class="{
                                                                'bg-indigo-500 border-indigo-600 text-white shadow-lg': workDays
                                                                    .includes({{ $index }}),
                                                                'bg-gray-50 border-gray-300 text-gray-600 hover:bg-gray-100':
                                                                    !workDays.includes({{ $index }})
                                                            }">
                                                            <div class="text-2xl mb-1">{{ $dayIcons[$index] }}</div>
                                                            <div class="text-xs font-bold">{{ $day }}</div>
                                                            <div class="text-xs mt-1"
                                                                x-show="workDays.includes({{ $index }})"
                                                                x-transition>
                                                                <i class="fa-solid fa-check"></i>
                                                            </div>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="settings[work_days]"
                                                :value="JSON.stringify(workDays)">

                                            {{-- Summary --}}
                                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-blue-800 font-medium">
                                                        <i class="fa-solid fa-calendar-days mr-2"></i>
                                                        Total Hari Kerja:
                                                    </span>
                                                    <span class="text-blue-900 font-bold text-lg"
                                                        x-text="workDays.length"></span>
                                                </div>
                                            </div>
                                        @elseif($setting->type === 'boolean')
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="settings[{{ $setting->key }}]"
                                                    value="1" {{ $setting->value ? 'checked' : '' }}
                                                    class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                                </div>
                                                <span class="ml-3 text-sm font-medium text-gray-700">
                                                    {{ $setting->value ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </label>
                                        @elseif($setting->type === 'integer')
                                            <div class="flex items-center gap-2">
                                                @if (str_contains($setting->key, 'deduction') || str_contains($setting->key, 'upah'))
                                                    <span class="text-gray-700 font-medium">Rp</span>
                                                @endif
                                                <x-text-input type="number" name="settings[{{ $setting->key }}]"
                                                    :value="$setting->value" class="block mt-1 w-full max-w-xs" min="0"
                                                    step="{{ str_contains($setting->key, 'hours') ? '0.5' : '1' }}" />
                                                @if (str_contains($setting->key, 'hours'))
                                                    <span class="text-gray-700">jam</span>
                                                @endif
                                            </div>
                                        @else
                                            <x-text-input type="text" name="settings[{{ $setting->key }}]"
                                                :value="$setting->value" class="block mt-1 w-full" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <div class="flex justify-between items-center mt-6 pt-6 border-t">
                            <form method="POST" action="{{ route('settings.reset') }}"
                                onsubmit="return confirm('Yakin ingin reset semua pengaturan ke default?')">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                                    <i class="fa-solid fa-undo mr-2"></i>
                                    Reset ke Default
                                </button>
                            </form>

                            <x-primary-button>
                                <i class="fa-solid fa-save mr-2"></i>
                                Simpan Pengaturan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <i class="fa-solid fa-lightbulb text-blue-600 text-2xl mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-2">💡 Tips Pengaturan</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Hari Kerja:</strong> Klik pada hari untuk toggle on/off. Hari yang aktif akan
                                berwarna biru.</li>
                            <li><strong>Perubahan Langsung:</strong> Pengaturan akan diterapkan ke semua modul setelah
                                disimpan.</li>
                            <li><strong>Upah Dosen:</strong> Nominal per SKS per pertemuan untuk perhitungan gaji dosen.
                            </li>
                            <li><strong>Cache:</strong> Pengaturan di-cache untuk performa. Clear cache otomatis saat
                                update.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function settingsForm() {
                return {
                    workDays: @json(json_decode($settings['attendance']->firstWhere('key', 'work_days')->value ?? '[]', true)),

                    init() {
                        console.log('Initial work days:', this.workDays);
                    },

                    toggleWorkDay(day) {
                        const index = this.workDays.indexOf(day);
                        if (index > -1) {
                            // Remove if exists
                            this.workDays.splice(index, 1);
                        } else {
                            // Add if not exists
                            this.workDays.push(day);
                        }
                        // Sort array
                        this.workDays.sort((a, b) => a - b);
                        console.log('Updated work days:', this.workDays);
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
