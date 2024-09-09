@props([
    'cols' => 3,
    'flow' => 'row'
])

<div {{ $attributes->merge([
    'class' => 'grid grid-cols-' . $cols . ' grid-flow-' . $flow
]) }}>
    {{ $slot }}
</div>
