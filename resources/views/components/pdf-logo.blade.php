@props([
    'logoPath' => null,
    'width' => '100',
])

@if ($logoPath && file_exists(public_path('storage/' . $logoPath)))
    @php
        $path = public_path('storage/' . $logoPath);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logoSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
    @endphp
    <img src="{{ $logoSrc }}" alt="Logo" width="{{ $width }}">
@else
    {{-- Tampilkan teks jika tidak ada logo --}}
    <span style="font-size: 18px; font-weight: bold;">NAMA PERUSAHAAN</span>
@endif
