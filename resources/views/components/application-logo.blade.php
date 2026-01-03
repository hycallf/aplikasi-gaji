@props(['logoPath' => null])

@php
    $src = null;
    if ($logoPath) {
        // 1. Cek apakah ini file default (misal diawali 'images/')
        // Kita cek apakah file fisiknya ada di folder public
        if (file_exists(public_path($logoPath))) {
            $src = asset($logoPath);
        }
        // 2. Jika tidak, asumsikan ini file hasil upload di Storage
        else {
            $src = asset('storage/' . $logoPath);
        }
    } else {
        // Fallback jika logoPath kosong (opsional)
        $src = asset('images/Logo.png');
    }
@endphp

@if ($src)
    <img src="{{ $src }}" {{ $attributes }} alt="Logo Perusahaan">
@else
    {{-- SVG Default jika gambar benar-benar tidak ditemukan --}}
    <svg {{ $attributes }} viewBox="0 0 317 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="..." fill="currentColor"/>
    </svg>
@endif
