<div class="flex flex-col items-center w-full md:flex-row">
    @php($color = current_team()->colorIsLight() ? 'text-black' : 'text-white')
    <h1 {{ $attributes->merge(['class' => 'font-semibold text-3xl leading-tight items-center flex-1 ' . $color]) }}>
        {{ $slot }}

        @if(isset($suffix))
            <svg xmlns="http://www.w3.org/2000/svg" class=" inline-block h-5 w-5" viewBox="0 0 20 20"
                 fill="currentColor">
                <path fill-rule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clip-rule="evenodd"/>
            </svg>
            {{ $suffix }}
        @endif
    </h1>

    @if(isset($actions))
        <div class="mt-4 md:mt-0">
            {{ $actions }}
        </div>
    @endif

</div>
