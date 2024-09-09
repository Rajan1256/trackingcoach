<x-app-layout>
    @include('components.charts.reports.monthly_js')
    @if(isset($minimal) && $minimal)
        <x-slot name="hideNavigation"></x-slot>
    @endif

    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $data->get('customer')->name]) }}
            <x-slot name="suffix">{{ __('Monthly scorecard :monthYear', ['monthYear' => $data->get('days')->first()->formatLocalized('%B %Y')]) }}</x-slot>

            <x-slot name="actions">
                @if(isset($minimal) && $minimal)
                    <x-button.big
                            icon="far fa-chevron-left"
                            href="javascript:history.go(-1);"
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()">
                        Go back
                    </x-button.big>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>


    <x-slot name="extras">
        <dl class="grid grid-cols-1 gap-8 sm:grid-cols-3 md:grid-cols-3">
            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Response ratio') }}<br/>
                    {{ $data->get('response_ratio') }}%
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $data->get('unique_days') }}/{{ $data->get('days')->count() }}
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Overall score') }}<br/>
                    {{ __('This month') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ round($data->get('overall_score')) }}
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Overall score') }}<br/>
                    {{ __('All time')  }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $data->get('customer')->getOverallScore() }}
                </dd>
            </div>

        </dl>
    </x-slot>

    <x-components::accordion>
        <x-components::accordion.header>
            <div class="flex justify-end w-full text-right">
                <div class="w-20">Start</div>
                <div class="w-20">Target</div>
                <div class="w-20">Results</div>
                <div class="w-20">Score</div>
            </div>
        </x-components::accordion.header>

        @foreach ($data->get('matrix') as $matri)
            <x-components::accordion.item hideButton x-on:click.prevent="draw{{ $matri['question_id'] }}()">
                <x-slot name="title">
                    <div class="w-full">
                        @if ( $matri['score'] >= 80 )
                            <i class="far fa-thumbs-up text-green-600"></i>
                        @elseif ( $matri['score'] <= 60 )
                            <i class="far fa-thumbs-down text-red-600"></i>
                        @else
                            <i class="far fa-thumbs-down text-yellow-500 rotate-90"></i>
                        @endif
                        {{ $matri['name'] }}
                    </div>
                    <div class="flex justify-end w-full text-right">
                        <div class="w-20">{{ one_decimal($matri['start']) }}</div>
                        <div class="w-20">{{ one_decimal($matri['target']) }}</div>
                        <div class="w-20">{{ one_decimal($matri['value']) }}</div>
                        <div class="w-20">{{ $matri['score'] }}</div>
                    </div>
                </x-slot>
                <div class="text-4xl w-full">
                    @include('components.charts.reports.monthly')
                </div>
            </x-components::accordion.item>
        @endforeach
    </x-components::accordion>
</x-app-layout>
