<x-slot name="description">
    <label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2 font-bold']) }}>
        {{ $slot }}
    </label>
</x-slot>
