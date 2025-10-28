@props(['type' => 'button', 'variant' => 'primary', 'size' => 'default', 'loading' => false, 'icon' => null])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-2xl transition-all duration-200 touch-manipulation transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = match($size) {
    'small' => 'h-10 px-4 text-sm min-w-[80px]',
    'large' => 'h-16 px-8 text-xl min-w-[120px]',
    default => 'h-14 px-6 text-lg min-w-[100px]'
};

$variantClasses = match($variant) {
    'primary' => 'bg-blue-500 hover:bg-blue-600 text-white shadow-lg hover:shadow-xl focus:ring-blue-500',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800 shadow-md hover:shadow-lg focus:ring-gray-400',
    'success' => 'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl focus:ring-green-500',
    'danger' => 'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl focus:ring-red-500',
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white shadow-lg hover:shadow-xl focus:ring-yellow-500',
    'outline' => 'border-2 border-blue-500 text-blue-500 hover:bg-blue-50 focus:ring-blue-500',
    default => 'bg-blue-500 hover:bg-blue-600 text-white shadow-lg hover:shadow-xl focus:ring-blue-500'
};

$classes = $baseClasses . ' ' . $sizeClasses . ' ' . $variantClasses;
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($loading) disabled @endif>
    @if($loading)
        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-current mr-2"></div>
        <span>Duke ngarkuar...</span>
    @else
        @if($icon)
            <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    @endif
</button>
