{{-- @formatter:off --}}
<x-app-layout>
    @if(isset($minimal) && $minimal)
        <x-slot name="hideNavigation"></x-slot>
    @endif

    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $data->get('client')->name]) }}
            <x-slot name="suffix">{{ __('Scorecard week :week', ['week' => $data->get('week')]) }}</x-slot>

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
        <dl class="grid grid-cols-2 gap-8 sm:grid-cols-4 md:grid-cols-4">
            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Response ratio') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $data->get('unique_days') }}/{{ $data->get('days_per_week') }}
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Weekly rating') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    @if ($data->get('overall_score') <= 60)
                        <i class="far fa-thumbs-down text-red-600"></i>
                    @elseif($data->get('overall_score') <= 80)
                        <i class="far fa-thumbs-down rotate-90 text-yellow-500"></i>
                    @else
                        <i class="far fa-thumbs-up text-green-600"></i>
                    @endif
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Overall score') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $data->get('overall_score') }}
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('On target') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $data->get('topsAndDips')->where('weekly_score', 100)->count() . "/" . $data->get('topsAndDips')->count()}}
                </dd>
            </div>
        </dl>
    </x-slot>

    @if ($data->get('unique_days') < $data->get('days_per_week') * 0.6)
        <x-components::alert.error>{{ __('Due to a low response this week, the scores could not be calculated precisely.') }}</x-components::alert.error>
    @endif


    <div class="w-full bg-white py-3 px-6 my-5">
        <h1 class="text-5xl font-bold my-5">{{ __("Your week") }}</h1>
        <p class="mb-4">
            {{ __("The overall scores of the days (items with week-targets excluded)") }}
        </p>
        <x-components::grid cols="{{ $data->get('days_per_week') === 5 ? 5 : 4 }}">
            @foreach ($data->get('days') as $day)
                <x-components::grid.block class="pb-5">
                    <span class="font-bold">{{ date_format_helper($day)->get_short_day_with_date() }}</span>
                    <br/>
                    @if ( $data->get('daily_summary')->get($day->format('N'), null) !== null)
                        <span class="text-5xl font-bold">{{ $data->get('daily_summary')->get($day->format('N')) }}</span>
                    @else
                        <span class="text-9xl font-bold leading-10">-</span>
                    @endif
                </x-components::grid.block>
            @endforeach
        </x-components::grid>
    </div>

    <div class="w-full bg-white p-3 my-5">
        <h1 class="text-5xl font-bold mb-5 mt-3 pl-3">{{ __("Your timeline") }}</h1>
        @include('components.charts.reports.weekly')
    </div>

    <x-components::accordion>
        <x-components::accordion.header>
            <div class="flex justify-end w-full text-right">
                <div class="w-20">Start</div>
                <div class="w-20">Target</div>
                <div class="w-20">Results</div>
                <div class="w-20">Score</div>
            </div>
        </x-components::accordion.header>

        @foreach ($data->get('topsAndDips') as $matri)
            <x-components::accordion.item hideButton cancelClick>
                <x-slot name="title">
                    <div class="w-full">
                        @if ( $matri['weekly_score'] >= 80 )
                            <i class="far fa-thumbs-up text-green-600"></i>
                        @elseif ( $matri['weekly_score'] <= 60 )
                            <i class="far fa-thumbs-down text-red-600"></i>
                        @else
                            <i class="far fa-thumbs-down text-yellow-500 rotate-90"></i>
                        @endif
                        {{ $matri['name'] }}
                    </div>
                    <div class="flex justify-end w-full text-right">
                        <div class="w-20">{{ one_decimal($matri['start']) }}</div>
                        <div class="w-20">{{ one_decimal($matri['target']) }}</div>
                        <div class="w-20">{{ one_decimal($matri['result']) }}</div>
                        <div class="w-20">{{ one_decimal($matri['weekly_score']) }}</div>
                    </div>
                </x-slot>
                <x-components::grid cols="{{ $data->get('days_per_week') === 5 ? 5 : 4 }}" class="w-full">
                    @foreach ($matri['days'] as $index => $dayData)
                        <x-components::grid.block class="pb-5">
                            <h4 class="font-bold text-xl">{{ $dayData['date']->formatLocalized('%A') }}</h4>
                            <p>
                                <i class="far fa-pencil"></i> &nbsp; {{ $dayData['answer'] }} <br/>
                                @if ($dayData['score'] !== null)
                                    <i class="fas fa-futbol"></i> &nbsp; {{ $dayData['score'] }}% <br/>
                                @endif
                                @if ($dayData['target'] !== null)
                                    <i class="far fa-bullseye"></i> &nbsp; {{ $dayData['target'] }} <br/>
                                @endif
                            </p>
                        </x-components::grid.block>
                    @endforeach
                </x-components::grid>
            </x-components::accordion.item>
        @endforeach
    </x-components::accordion>
</x-app-layout>
