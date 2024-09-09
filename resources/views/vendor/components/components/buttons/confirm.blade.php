@props([
    'primary' => false,
    'name' => null,
    'title' => 'Are you sure?',
    'firstButton' => 'OK',
    'secondButton' => 'Cancel',
    'method' => 'POST',
    'disabled' => false,
])

@php
    if ($primary) {
        $classes = 'cursor-pointer -ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-indigo-600 text-sm font-medium text-white hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500';
    } else {
        $classes = 'cursor-pointer -ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500';
    }
@endphp

<div x-data="{ {{ $name }}: false }" class="inline-block">
    <button type="button"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => $classes . ' ' . (($disabled) ? 'bg-gray-200 hover:bg-gray-200' : '')]) }}
            @click="{{ $name }} = !{{$name}}">{{ $slot }}</button>
    <x-components::modal.confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                 :secondButton="$secondButton"
                                 :action="$attributes->get('href')"
                                 :method="$method">
        {{ $modalMessage }}
    </x-components::modal.confirm>
</div>
