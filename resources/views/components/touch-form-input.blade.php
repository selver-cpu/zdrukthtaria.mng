@props(['type' => 'text', 'name', 'value' => '', 'placeholder' => '', 'required' => false, 'autocomplete' => null, 'suggestions' => []])

@php
$classes = 'w-full h-14 px-4 text-lg bg-white border-2 border-gray-200 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all duration-200 touch-manipulation shadow-sm placeholder-gray-400';
@endphp

<div class="relative">
    @if($type === 'date')
        <input type="date" 
               name="{{ $name }}" 
               value="{{ $value }}"
               {{ $attributes->merge(['class' => $classes]) }}
               {{ $required ? 'required' : '' }}>
    @elseif($type === 'datetime-local')
        <input type="datetime-local" 
               name="{{ $name }}" 
               value="{{ $value }}"
               {{ $attributes->merge(['class' => $classes]) }}
               {{ $required ? 'required' : '' }}>
    @elseif($type === 'select')
        <select name="{{ $name }}" 
                {{ $attributes->merge(['class' => $classes . ' appearance-none cursor-pointer']) }}
                {{ $required ? 'required' : '' }}>
            {{ $slot }}
        </select>
        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    @elseif($type === 'textarea')
        <textarea name="{{ $name }}" 
                  placeholder="{{ $placeholder }}"
                  {{ $attributes->merge(['class' => $classes . ' min-h-[120px] resize-none']) }}
                  {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
    @else
        <input type="{{ $type }}" 
               name="{{ $name }}" 
               value="{{ $value }}"
               placeholder="{{ $placeholder }}"
               {{ $attributes->merge(['class' => $classes]) }}
               {{ $required ? 'required' : '' }}
               {{ $autocomplete ? 'autocomplete=' . $autocomplete : '' }}
               @if(count($suggestions) > 0) list="{{ $name }}_suggestions" @endif>
        
        @if(count($suggestions) > 0)
            <datalist id="{{ $name }}_suggestions">
                @foreach($suggestions as $suggestion)
                    <option value="{{ $suggestion }}">
                @endforeach
            </datalist>
        @endif
    @endif
    
    @if($type !== 'select' && $type !== 'textarea')
        <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
            @if($type === 'date' || $type === 'datetime-local')
                <svg class="h-6 w-6 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 2m8-2l2 2m-2-2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6"></path>
                </svg>
            @elseif($required)
                <span class="text-red-500 text-lg">*</span>
            @endif
        </div>
    @endif
</div>
