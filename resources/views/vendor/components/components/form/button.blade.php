@props([
    'submit' => false,
    'primary' => false,
    'action' => false,
    'ml' => 3
])

@php
    if ($primary) {
        $classes = "ml-{$ml} inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500";
    } else {
        $classes = "ml-{$ml} inline-flex justify-center py-2 px-4 shadow-sm text-sm font-medium rounded-md text-black border border-gray-300 bg-white hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500";
    }
@endphp

@if ($action)
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $submit ? 'submit' : 'button' }}"
            {{ $attributes->except('type')->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
