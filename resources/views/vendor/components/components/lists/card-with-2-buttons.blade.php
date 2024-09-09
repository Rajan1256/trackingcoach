@props([
    'img' => '',
    'header' => '',
    'subHeader' => '',
])
<li {{ $attributes->except('href')->merge([
    'class' => 'col-span-1 flex flex-col text-center bg-white rounded-lg shadow divide-y divide-gray-200'
]) }}>
    <a href="{{ $attributes->get('href') }}">
        <div class="flex-1 flex flex-col p-8">
            <img class="w-32 h-32 flex-shrink-0 mx-auto rounded-full" src="{{ $img }}" alt="">
            <h3 class="mt-6 text-gray-900 text-sm font-medium">{{ $header }}</h3>
            <dl class="mt-1 flex-grow flex flex-col justify-between">
                <dd class="text-gray-500 text-sm">{!! $subHeader  !!}</dd>
            </dl>
        </div>
    </a>
    @if (!empty($firstButton) || !empty($secondButton))
        <div>
            <div class="-mt-px flex divide-x divide-gray-200">
                @if(!empty($firstButton))
                    <div class="w-0 flex-1 flex">
                        {{ $firstButton }}
                    </div>
                @endif
                @if(!empty($secondButton))
                    <div class="-ml-px w-0 flex-1 flex">
                        {{ $secondButton }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</li>
