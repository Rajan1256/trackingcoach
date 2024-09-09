@props([
    'action' => ''
])

<li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
    <div class="w-0 flex-1 flex items-center">
        <span class="ml-2 flex-1 w-0 truncate">
            {{ $slot }}
        </span>
    </div>
    @if(!empty($action))
    <div class="ml-4 flex-shrink-0">
        <a {{ $attributes->merge([
            'class' => 'font-medium text-indigo-600 hover:text-indigo-500'
        ]) }}>
            {{ $action }}
        </a>
    </div>
    @endif
</li>
