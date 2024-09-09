@props([
    'defaultStyle' => 'max-w-lg space-y-4'
])

<x-slot name="input">
    <div class="mt-4 sm:mt-0 sm:col-span-2">
        <div {{ $attributes->merge(['class' => $defaultStyle]) }}>
            {{ $slot }}
        </div>
    </div>
</x-slot>
