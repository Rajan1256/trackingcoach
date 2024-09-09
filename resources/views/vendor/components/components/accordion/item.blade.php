@props([
    'title' => '',
    'hideButton' => false,
    'itemClasses' => 'px-8 py-8',
    'cancelClick' => false,
])

<li {{ $attributes->merge([
    'class' => "relative border-b border-gray-200",
]) }} x-data="{show: false}">
    <button type="button" class="w-full px-8 py-6 text-left"
            @if (!$cancelClick) x-on:click.prevent="show = !show" @endif>
        <div class="flex items-center justify-between">
            @if(!$hideButton)
                <span>
            @endif
                    {!! $title !!}
                    @if(!$hideButton)
                </span>
                <div class="ml-5 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" x-show="!show">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" x-show="show">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
            @endif
        </div>
    </button>

    <div class="relative overflow-hidden transition-all max-h-0 duration-700 bg-white" style="" x-ref="container1"
         x-bind:style="show ? 'max-height: ' + $refs.container1.scrollHeight + 'px' : ''">
        <div class="{{ $itemClasses }}">
            {{ $slot }}
        </div>
    </div>
</li>
