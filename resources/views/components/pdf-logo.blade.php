@props([
    'width' => '150',
])

@php
    // Tentukan path ke file logo Anda di dalam folder public
    $path = public_path('images/Logo.png');

    // Inisialisasi variabel untuk menghindari error
    $logoSrc = '';

    // Cek jika file benar-benar ada sebelum mencoba membacanya
    if (file_exists($path)) {
        // Baca file gambar
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        // Ubah gambar menjadi format Base64 dan buat Data URI
        $logoSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp

{{-- Tampilkan gambar hanya jika sumber Base64 berhasil dibuat --}}
@if ($logoSrc)
    <img src="{{ $logoSrc }}" alt="Logo" width="{{ $width }}">
@else
    {{-- Tampilkan teks ini jika logo tidak ditemukan di path --}}
    <span>Logo Tidak Ditemukan</span>
@endif
