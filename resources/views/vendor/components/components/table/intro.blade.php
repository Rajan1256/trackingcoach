<x-slot name="intro">
    <div class="bg-white px-4 py-5 sm:px-6">
        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
            @if(!empty($title) || !empty($description))
                <div class="ml-4 mt-4 w-full">
                    @if (!empty($title))
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $title ?? '' }}
                        </h3>
                    @endif
                    @if(!empty($description))
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $description }}
                        </p>
                    @endif
                </div>
            @endif
            @if(!empty($buttons))
                <div class="flex justify-end w-full">
                    {{ $buttons }}
                </div>
            @endif
        </div>
    </div>
</x-slot>
