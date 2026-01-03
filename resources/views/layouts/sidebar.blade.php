{{-- Sidebar - UPDATED --}}
<aside class="bg-gray-800 text-gray-300 w-64 fixed inset-y-0 left-0 z-30 transform transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <div class="flex flex-col h-full">
        {{-- Header Logo --}}
        <div class="h-16 flex-shrink-0 flex items-center justify-center border-b border-gray-700 px-4">
            <a href="{{ Auth::user()->role === 'operator' ? route('dashboard') : route('user.dashboard') }}"
                class="flex items-center gap-2">
                <x-application-logo :logoPath="$companyProfile?->logo" class="block h-9 w-auto" />
                <span class="text-white font-bold">{{ $companyProfile->nama_perusahaan ?? config('app.name') }}</span>
            </a>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 overflow-y-auto pb-4 custom-scrollbar">
            <nav class="px-2 py-4 space-y-1">
                @if (Auth::user()->role === 'operator')
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
            {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-home fa-fw w-5 h-5 mr-3 text-center"></i>
                        Dashboard
                    </a>

                    <p class="px-4 pt-4 pb-2 text-xs text-gray-500 uppercase">Manajemen Karyawan</p>

                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                {{ request()->routeIs('users.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-user fa-fw w-5 h-5 mr-3 text-center"></i>
                        Users
                    </a>

                    <a href="{{ route('employees.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('employees.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-address-book fa-fw w-5 h-5 mr-3 text-center"></i>
                        Karyawan & Dosen
                    </a>

                    {{-- MENU BARU: Akademik --}}
                    <p class="px-4 pt-4 pb-2 text-xs text-gray-500 uppercase">Akademik</p>

                    <a href="{{ route('academic-years.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                        {{ request()->routeIs('academic-years.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-alt fa-fw w-5 h-5 mr-3 text-center"></i>
                        Tahun Ajaran
                    </a>

                    <a href="{{ route('matkuls.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                        {{ request()->routeIs('matkuls.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-book fa-fw w-5 h-5 mr-3 text-center"></i>
                        Mata Kuliah
                    </a>

                    <a href="{{ route('enrollments.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                        {{ request()->routeIs('enrollments.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-chalkboard-user fa-fw w-5 h-5 mr-3 text-center"></i>
                        Enrollment Dosen
                    </a>

                    <p class="px-4 pt-4 pb-2 text-xs text-gray-500 uppercase">Kelola Gaji</p>

                    <div x-data="{ open: {{ request()->routeIs('recap.*') || request()->routeIs('attendances.*') || request()->routeIs('overtimes.*') || request()->routeIs('dosen.attendances.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-md transition duration-200 hover:bg-gray-700 hover:text-white">
                            <span class="flex items-center">
                                <i class="fa-solid fa-file-pen fa-fw w-5 h-5 mr-3 text-center"></i>
                                Rekapitulasi
                            </span>
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="mt-1 pl-8 space-y-1">
                            <a href="{{ route('recap.index') }}"
                                class="flex items-center w-full px-4 py-2 rounded-md text-sm {{ request()->routeIs('recap.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                                <i class="fa-solid fa-list-check fa-fw w-5 h-5 mr-3 text-center"></i>
                                Rekap Bulanan
                            </a>
                            <a href="{{ route('attendances.index') }}"
                                class="flex items-center w-full px-4 py-2 rounded-md text-sm {{ request()->routeIs('attendances.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                                <i class="fa-solid fa-calendar-day fa-fw w-5 h-5 mr-3 text-center"></i>
                                Absensi Karyawan
                            </a>
                            <a href="{{ route('dosen.attendances.index') }}"
                                class="flex items-center w-full px-4 py-2 rounded-md text-sm {{ request()->routeIs('dosen.attendances.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                                <i class="fa-solid fa-graduation-cap fa-fw w-5 h-5 mr-3 text-center"></i>
                                Absensi Dosen
                            </a>
                            <a href="{{ route('overtimes.index') }}"
                                class="flex items-center w-full px-4 py-2 rounded-md text-sm {{ request()->routeIs('overtimes.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                                <i class="fa-solid fa-stopwatch fa-fw w-5 h-5 mr-3 text-center"></i>
                                Input Lembur
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('events.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('events.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-gift fa-fw w-5 h-5 mr-3 text-center"></i>
                        Event & Insentif
                    </a>

                    <a href="{{ route('deductions.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('deductions.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar fa-fw w-5 h-5 mr-3 text-center"></i>
                        Potongan
                    </a>

                    <p class="px-4 pt-4 pb-2 text-xs text-gray-500 uppercase">Slip gaji & laporan</p>

                    <a href="{{ route('payroll.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('payroll.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-calculator fa-fw w-5 h-5 mr-3 text-center"></i>
                        Proses Gaji
                    </a>

                    <div class="border-t border-gray-700 mt-4 pt-4">
                        {{-- MENU BARU: Settings --}}
                        <a href="{{ route('settings.index') }}"
                            class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('settings.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <i class="fa-solid fa-cog fa-fw w-5 h-5 mr-3 text-center"></i>
                            Pengaturan
                        </a>

                        <a href="{{ route('company.profile.edit') }}"
                            class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('company.profile.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <i class="fa-solid fa-building fa-fw w-5 h-5 mr-3 text-center"></i>
                            Profil Perusahaan
                        </a>
                    </div>
                @else
                    {{-- MENU UNTUK KARYAWAN & DOSEN --}}
                    <a href="{{ route('user.dashboard') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                {{ request()->routeIs('user.dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-home fa-fw w-5 h-5 mr-3 text-center"></i>
                        Dashboard Saya
                    </a>

                    <a href="{{ route('user.payroll.history') }}"
                        class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                {{ request()->routeIs('user.payroll.history') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-receipt fa-fw w-5 h-5 mr-3 text-center"></i>
                        Riwayat Gaji
                    </a>
                @endif
            </nav>
        </div>

        <div class="flex-shrink-0 border-t border-gray-700">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center px-4 py-4 rounded-md transition duration-200 hover:bg-gray-700 hover:text-white">
                <i class="fa-solid fa-user-edit fa-fw w-5 h-5 mr-3 text-center"></i>
                Edit Profil
            </a>
        </div>
    </div>
</aside>
