@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex h-10 items-center justify-center gap-1 px-4 rounded-md text-sm font-medium leading-none whitespace-nowrap bg-blue-600 text-white hover:bg-blue-500 active:bg-blue-700 active:scale-[.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out'
            : 'inline-flex h-10 items-center justify-center gap-1 px-4 rounded-md text-sm font-medium leading-none whitespace-nowrap text-blue-700 bg-blue-50 hover:bg-blue-100 active:bg-blue-200 active:scale-[.98] border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
