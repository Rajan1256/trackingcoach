<div class="bg-white overflow-hidden sm:rounded-lg py-3">
    {{ $intro ?? '' }}
    <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
            {{ $slot }}
        </dl>
    </div>
    @if (!empty($buttons))
        <div class="p-5 border-t border-gray-200">
            <div class="flex justify-end">
                {{ $buttons }}
            </div>
        </div>
    @endif
</div>
