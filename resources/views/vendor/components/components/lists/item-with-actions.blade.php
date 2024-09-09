<li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
    <div class="w-0 flex-1 flex items-center">
        <span class="ml-2 flex-1 w-0">
            {{ $slot }}
        </span>
    </div>
    @if($between)
        {{ $between }}
    @endif
    <div class="ml-4 flex-shrink-0">
        {{ $actions }}
    </div>
</li>
