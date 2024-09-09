@props([
    'stats' => []
])


<dl class="flex justify-between">
    @foreach ($stats as $key => $value)
        <div class="flex flex-col">
            <dt class="order-1 mt-2 text-lg leading-6 font-medium text-white">
                {!! $key !!}
            </dt>
            <dd class="order-2 text-5xl font-bold text-white">
                {!! $value !!}
            </dd>
        </div>
    @endforeach
</dl>
