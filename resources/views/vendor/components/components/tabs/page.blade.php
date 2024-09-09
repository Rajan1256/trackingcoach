@props([
    'name' => ''
])

<div x-bind:class="activeTab === '{{ $name }}' ? '' : 'hidden'">
    {{ $slot }}
</div>
