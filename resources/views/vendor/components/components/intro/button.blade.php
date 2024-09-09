@props([
    'primary' => false,
    'delete' => false,
    'name' => 'confirm',
    'title' => 'Are you sure?',
    'firstButton' => 'OK',
    'secondButton' => 'Cancel',
])

@php
    if ($primary) {
        $classes = 'ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
    } else {
        $classes = 'ml-3 inline-flex justify-center py-2 px-4 shadow-sm text-sm font-medium rounded-md text-black border border-gray-300 bg-white hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
    }
@endphp

@if($delete)
    <div x-data="{ {{ $name }}: false }" class="inline-block">
        <button type="button"
                class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="{{ $name }} = !{{$name}}">{{ $slot }}</button>
        <x-components::modal.delete-confirm :name="$name" :title="$title" :firstButton="$firstButton"
                                            :secondButton="$secondButton"
                                            :action="$attributes->get('href')">
            {{ $modalMessage }}
        </x-components::modal.delete-confirm>
    </div>
@else
    <a {{ $attributes->merge([
    'type' => 'button',
    'class' => $classes
]) }}>
        {{ $slot }}
    </a>
@endif
