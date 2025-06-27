@props([
    'type' => 'edit', // Tipe default
    'href' => '#', // URL untuk link
    'route' => '#', // Route untuk form
    'dataUrl' => '',
])

{{-- Tombol SHOW / DETAIL --}}
@if ($type == 'show')
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' =>
                'inline-flex items-center gap-x-1.5 rounded-md bg-green-100 dark:bg-green-900/50 px-2.5 py-1.5 text-sm font-semibold text-green-700 dark:text-green-300 shadow-sm hover:bg-green-200 dark:hover:bg-green-800 transition-colors',
        ]) }}>

        <i class="fa-solid fa-eye fa-fw"></i>

        <span>Detail</span>
    </a>
@endif


@if ($type == 'edit')
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' => 'inline-flex items-center gap-x-1.5 rounded-md px-2.5 py-1.5 text-xs font-semibold transition-colors
                                                                    bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                                                                    hover:bg-blue-200
                                                                    dark:bg-blue-900/50 dark:text-blue-300 dark:ring-blue-300/20 dark:hover:bg-blue-800',
        ]) }}>

        {{-- Ikon Edit dari Font Awesome --}}
        <i class="fa-solid fa-pencil fa-fw"></i>
    </a>
@endif

@if ($type == 'delete')
    <form action="{{ $route }}" method="POST" class="inline" onsubmit="confirmDelete(event)">
        @csrf
        @method('DELETE')
        <button type="submit"
            {{ $attributes->merge([
                'class' => 'inline-flex items-center gap-x-1.5 rounded-md px-2.5 py-1.5 text-xs font-semibold transition-colors
                                                                                                bg-red-50 text-red-700 ring-1 ring-inset ring-red-700/10
                                                                                                hover:bg-red-200
                                                                                                dark:bg-red-900/50 dark:text-red-300 dark:ring-red-300/20 dark:hover:bg-red-800',
            ]) }}>

            {{-- Ikon Hapus dari Font Awesome --}}
            <i class="fa-solid fa-trash-can fa-fw"></i>
        </button>
    </form>
@endif
