@props([
    'cards' => false,
    'ol' => false,
])

@if ($cards)
    <ul {{ $attributes->merge([
    'role' => 'list',
    'class' => 'grid grid-cols-1 gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
]) }}>
        {{ $slot }}
    </ul>
@elseif($ol)
    <ol {{ $attributes->merge([
    'class' => 'border border-gray-200 rounded-md divide-y divide-gray-200'
]) }}>
        {{ $slot }}
    </ol>
@else
    <ul {{ $attributes->merge([
    'class' => 'border border-gray-200 rounded-md divide-y divide-gray-200'
]) }}>
        {{ $slot }}
    </ul>
@endif
