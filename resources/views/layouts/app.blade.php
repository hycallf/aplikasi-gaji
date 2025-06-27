<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    {{-- 1. BUNGKUS UTAMA DENGAN STATE ALPINE.JS --}}
    {{-- Secara default, sidebar terbuka di layar besar (lg) dan tertutup di layar kecil --}}
    <div x-data="{ sidebarOpen: window.innerWidth > 1024 ? true : false }" @resize.window="sidebarOpen = window.innerWidth > 1024 ? true : false"
        class="flex min-h-screen bg-gray-100 dark:bg-gray-900">

        {{-- 2. SERTAKAN SIDEBAR --}}
        {{-- Sidebar akan mengontrol state 'sidebarOpen' --}}
        @include('layouts.sidebar')

        {{-- 3. KONTAINER KONTEN UTAMA --}}
        {{-- Diberi margin kiri dinamis saat sidebar terbuka di layar besar --}}
        <div class="flex-1 flex flex-col transition-all duration-300" :class="{ 'lg:ml-64': sidebarOpen }">

            {{-- Navigasi Atas --}}
            {{-- Kita akan meneruskan state 'sidebarOpen' ke navigasi --}}
            @include('layouts.navigation', ['sidebarOpen' => 'sidebarOpen'])

            {{-- Area Konten Utama yang Bisa di-scroll --}}
            <div class="flex-1 overflow-x-hidden overflow-y-auto">
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    @if (session()->has('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false, // Tombol OK tidak ditampilkan
                timer: 2000 // Notifikasi hilang setelah 2 detik
            });
        </script>
    @endif

    <script>
        function confirmDelete(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Anda Yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
