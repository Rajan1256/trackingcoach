@props([
    'color' => 'gray'
])

<span {{ $attributes->merge([
    'class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{$color}-100 text-{$color}-800"
]) }}>
  <svg class="mr-1.5 h-2 w-2 text-{{ $color }}-400" fill="currentColor" viewBox="0 0 8 8">
    <circle cx="4" cy="4" r="3"/>
  </svg>
  {{ $slot }}
</span>
