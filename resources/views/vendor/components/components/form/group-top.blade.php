<div class="sm:col-span-6">
    @if(!empty($alert))
        {{ $alert }}
    @endif
    @if(!empty($label))
        {{ $label }}
    @endif
    @if(!empty($description))
        {{ $description }}
    @endif
    <div class="mt-1">
        {{ $input }}
    </div>
</div>
