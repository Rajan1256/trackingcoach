@props([
    'start' => ''
])

<div x-data="{ activeTab: '{{ $start }}'}">
    <div class="hidden sm:block">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                {{ $tabs }}
            </nav>
        </div>
    </div>

    @if(!empty($slot))
        {{ $slot }}
    @endif
</div>
