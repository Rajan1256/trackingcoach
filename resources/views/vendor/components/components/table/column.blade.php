@props([
    'actions' => false
])

@if(!$actions)
    <td {{ $attributes->merge(['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900']) }}>
        {{ $slot }}
    </td>
@else
    <td {{ $attributes->merge(['class' => 'px-6 py-4 whitespace-nowrap text-right text-sm font-medium']) }}>
        {{ $slot }}
    </td>
@endif
