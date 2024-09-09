@props([
    'id' => '',
    'name' => '',
    'disabled' => false
])

<x-slot name="input">
    <select id="{{ $id }}" name="{{ $name }}" @if($disabled) disabled @endif
    class="block focus:ring-indigo-500 focus:border-indigo-500 w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        {{ $slot }}
    </select>
</x-slot>
