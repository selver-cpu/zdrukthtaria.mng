@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full min-h-10 -mx-4 px-4 py-2.5 text-start text-base font-medium rounded-none bg-blue-600 text-white hover:bg-blue-500 active:bg-blue-700 active:scale-[.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out'
            : 'flex items-center w-full min-h-10 -mx-4 px-4 py-2.5 text-start text-base font-medium rounded-none text-blue-700 bg-blue-50 hover:bg-blue-100 active:bg-blue-200 active:scale-[.99] focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
