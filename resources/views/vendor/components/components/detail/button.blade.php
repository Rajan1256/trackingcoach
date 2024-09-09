@props([
    'submit' => false,
    'primary' => false,
])

@php
    if ($primary) {
        $classes = 'ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
    } else {
        $classes = 'ml-3 inline-flex justify-center py-2 px-4 shadow-sm text-sm font-medium rounded-md text-black border border-gray-300 bg-white hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
    }
@endphp

<button type="{{ $submit ? 'submit' : 'button' }}"
        class="{{ $classes }}">
    {{ $slot }}
</button>
