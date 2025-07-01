{{-- Sidebar --}}
{{-- 1. Tambahkan class untuk posisi fixed dan transisi --}}
<aside
    class="w-64 bg-gray-800 text-gray-300 h-screen flex flex-col fixed top-0 left-0 z-20 transform transition-transform duration-300 ease-in-out"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
    {{-- Logo Aplikasi --}}
    <div class="h-16 flex items-center justify-center border-b border-gray-700 px-4">
        <a href="..." class="flex items-center gap-2">
            <x-application-logo :logoPath="$companyProfile?->logo" class="block h-9 w-auto" />
            <span class="text-white font-bold">{{ $companyProfile->nama_perusahaan ?? config('app.name') }}</span>
        </a>
    </div>

    {{-- Menu Navigasi (tidak ada perubahan di sini) --}}
    <nav class="flex-1 px-2 py-4 space-y-2">
        {{-- ... (isi menu navigasi Anda tetap sama) ... --}}

        @if (Auth::user()->role === 'operator')
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-2.5 rounded-md transition duration-200
            {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <p class="px-4 pt-4 pb-2 text-xs text-gray-500 uppercase">Manajemen Karyawan</p>
            <a href="{{ route('users.index') }}"
                class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                {{ request()->routeIs('users.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                <i class="fa-solid fa-user w-5 h-5 mr-3 text-center"></i>
                Users
            </a>
            <a href="{{ route('employees.index') }}"
                class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('employees.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                <i class="fa-solid fa-address-book w-5 h-5 mr-3 text-center"></i>
                Karyawan
            </a>
            <p class="px-4 pt-4 pb-2 text-xs text-gray-500 uppercase">Kelola Gaji</p>

            <div x-data="{ open: {{ request()->routeIs('recap.*') || request()->routeIs('attendances.*') || request()->routeIs('overtimes.*') ? 'true' : 'false' }} }">
                {{-- Tombol Utama Dropdown --}}
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

                {{-- DIUBAH: Konten Sub-menu dengan ikon dan inden --}}
                <div x-show="open" x-transition class="mt-1 pl-8 space-y-1">

                    <a href="{{ route('recap.index') }}"
                        class="flex items-center w-full px-4 py-2 rounded-md text-sm {{ request()->routeIs('recap.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-list-check fa-fw w-5 h-5 mr-3 text-center"></i>
                        Rekap Bulanan
                    </a>
                    <a href="{{ route('attendances.index') }}"
                        class="flex items-center w-full px-4 py-2 rounded-md text-sm {{ request()->routeIs('attendances.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-day fa-fw w-5 h-5 mr-3 text-center"></i>
                        Input Absensi
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
                <a href="{{ route('company.profile.edit') }}"
                    class="flex items-center px-4 py-2.5 rounded-md
            {{ request()->routeIs('company.profile.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-building fa-fw w-5 h-5 mr-3 text-center"></i>
                    Profil Perusahaan
                </a>
            </div>
        @else
            {{-- ///////////// MENU UNTUK KARYAWAN & DOSEN ///////////// --}}

            {{-- Link Dashboard Karyawan --}}
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

        {{-- Link Profile (tersedia untuk semua role) --}}
        <div class="border-t border-gray-700 mt-4 pt-4">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center px-4 py-2.5 rounded-md transition duration-200
                {{ request()->routeIs('profile.edit') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                <i class="fa-solid fa-user-edit fa-fw w-5 h-5 mr-3 text-center"></i>
                Edit Profil
            </a>
        </div>
    </nav>
</aside>
