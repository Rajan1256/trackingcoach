@props([
    'color' => 'gray'
])

<span {{ $attributes->merge([
    'class' => "inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-{$color}-100 text-{$color}-800"
]) }}>
  {{ $slot }}
</span>
