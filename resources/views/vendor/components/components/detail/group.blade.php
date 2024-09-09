<div class="p-6 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
    <dt class="text-sm font-medium text-gray-500">
        {{ $label ?? '' }}
        @if(!empty($description))
            <br/>
            <span class="italic">{{ $description ?? '' }}</span>
        @endif
    </dt>
    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
        {{ $field ?? '' }}
    </dd>
</div>
