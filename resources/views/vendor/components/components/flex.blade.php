@props([
    'justify' => 'between'
])

<div {{ $attributes->merge([
    'class' => 'flex justify-' . $justify
]) }}>
    {{ $slot }}
</div>
