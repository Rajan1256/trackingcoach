@props([
    'name' => ''
])
<a {{ $attributes->merge([
        'class' => 'flex items-center px-3 py-2 text-md font-medium rounded-md'
    ]) }}
   x-bind:class="activeTab === '{{ $name }}' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
   @click="activeTab = '{{ $name }}'">
    {{ $slot }}
</a>
