@props([
    'iteration' => 1
])

<tr {{ $attributes->merge([
    'class' => ($iteration % 2 === 0) ? 'bg-gray-100' : 'bg-white'
]) }}>
    {{ $slot }}
</tr>
