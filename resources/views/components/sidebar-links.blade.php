@props([
    'start' => '',
    'customer' => ''
])
<x-slot name="sidebar">
    <section aria-labelledby="Customer navigation" x-data="{ activeTab: '{{ $start }}'}" id="sidebar-left">
        <div class="bg-white overflow-hidden shadow">
            <div class="bg-white p-6">
                {{ $slot }}
            </div>
        </div>
    </section>
</x-slot>
