@props([
    'name' => ''
])

<a {{ $attributes->merge([
        'class' => 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer'
    ]) }}
   x-bind:class="activeTab === '{{ $name }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
   @click="activeTab = '{{ $name }}'">
    {{ $slot }}
</a>
