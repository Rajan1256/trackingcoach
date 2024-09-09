<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('360 interview from :date', ['date' => date_format_helper($interview->date)->get_dmy() ]) }}</x-slot>
            <x-slot name="actions">
                <x-button.big
                        icon="far fa-chevron-left"
                        :href="route('customers.interviews.index', $customer)"
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()">
                    Go back
                </x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">
            
            @if(count($interview->continue))
                <div class="my-10">
                    <h1 class="font-bold text-gray-800 text-3xl mb-3">
                        <i class="fal fa-step-forward mr-2"></i> {{ __('Continue') }}</h1>
                    <p class="my-2 text-gray-600 italic">{{ __('What should I continue doing because it is effective and I am good at it?') }}</p>
                    <ul class="list-disc list-inside">
                        @foreach ($interview->continue as $continue)
                            <li>{{ $continue }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(count($interview->start))
                <div class="my-10">
                    <h1 class="font-bold text-gray-800 text-3xl mb-3">
                        <i class="fal fa-play mr-2"></i> {{ __('Start') }}</h1>
                    <p class="my-2 text-gray-600 italic">{{ __('What should I start doing what would make me more effective in my job and working with others?') }}</p>
                    <ul class="list-disc list-inside">
                        @foreach ($interview->start as $start)
                            <li>{{ $start }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(count($interview->stop))
                <div class="my-10">
                    <h1 class="font-bold text-gray-800 text-3xl mb-3">
                        <i class="fal fa-stop mr-2"></i> {{ __('Stop') }}</h1>
                    <p class="my-2 text-gray-600 italic">{{ __('What should I change or stop doing because it is not effective or maybe even counterproductive?') }}</p>
                    <ul class="list-disc list-inside">
                        @foreach ($interview->stop as $stop)
                            <li>{{ $stop }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(count($interview->best))
                <div class="my-10">
                    <h1 class="font-bold text-gray-800 text-3xl mb-3">
                        <i class="fal fa-thumbs-up mr-2"></i> {{ __('Best') }}</h1>
                    <p class="my-2 text-gray-600 italic">{{ __('What situations bring out the best in me?') }}</p>
                    <ul class="list-disc list-inside">
                        @foreach ($interview->best as $best)
                            <li>{{ $best }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif
            @if(count($interview->worst))
                <div class="my-10">
                    <h1 class="font-bold text-gray-800 text-3xl mb-3">
                        <i class="fal fa-thumbs-down mr-2"></i> {{ __('Worst') }}</h1>
                    <p class="my-2 text-gray-600 italic">{{ __('What situations bring out the worst in me?') }}</p>
                    <ul class="list-disc list-inside">
                        @foreach ($interview->worst as $worst)
                            <li>{{ $worst }}</li>
                        @endforeach
                    </ul>
                </div>

            @endif
        </div>
    </div>
</x-app-layout>
