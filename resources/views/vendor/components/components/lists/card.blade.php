@props([
    'header' => '',
    'sub' => ''
])

<li {{ $attributes->merge([
    'class' => 'col-span-1 flex flex-col bg-white rounded-lg shadow'
]) }}>
    @if(!empty($header) || !empty($sub))
        <div class="flex-1 flex flex-col py-3 px-5">
            <h3 class="text-gray-900 text-sm font-medium text-xl font-bold">{{ $header }}</h3>
            <h4 class="text-gray-900 text-sm font-medium text-base">{{ $sub }}</h4>
        </div>
    @endif
    <div>
        {{ $slot }}
    </div>
</li>
