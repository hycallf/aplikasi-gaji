@props(['logoPath' => null])

@if ($logoPath)
    <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" {{ $attributes }}>
@else
    {{-- Logo SVG default jika tidak ada logo di database --}}
    <svg {{ $attributes }} ...>
        ...
    </svg>
@endif
