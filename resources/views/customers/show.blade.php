<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Summary') }}</x-slot>
        </x-page-header>
    </x-slot>

    <x-slot name="extras">
        <dl class="grid grid-cols-1 gap-8 sm:grid-cols-3">
            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Filled out questionnaires') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $customer->entries()->tracklist()->count() }}
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Overall score') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{$overallScore}}
                </dd>
            </div>

            <div class="px-4 py-5 bg-white shadow overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Customer since') }}
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ date_format_helper( $customer->created_at)->get_dmy() }}
                </dd>
            </div>
        </dl>
    </x-slot>

    <div class="bg-white overflow-hidden">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">

            <x-components::table class="mb-10">
                <x-components::table.header>
                    <x-components::table.heading>{{ __('Name') }}</x-components::table.heading>
                    <x-components::table.heading>{{ __('Email address') }}</x-components::table.heading>
                    <x-components::table.heading>{{ __('Phone') }}</x-components::table.heading>
                    @if($customer->coach)
                        <x-components::table.heading>{{ __('Coach') }}</x-components::table.heading>
                    @endif
                    @if($customer->tracker)
                        <x-components::table.heading>{{ __('Tracker') }}</x-components::table.heading>
                    @endif
                    @if($customer->customerSchedules->count())
                        <x-components::table.heading>{{ __('Call time') }}</x-components::table.heading>
                    @endif
                </x-components::table.header>
                <x-components::table.body>
                    <x-components::table.column>{{ $customer->name }}</x-components::table.column>
                    <x-components::table.column>{{ $customer->email }}</x-components::table.column>
                    <x-components::table.column>{{ $settings->phone }}</x-components::table.column>
                    @if($customer->coach)
                        <x-components::table.column>{{ $customer->coach->name }}</x-components::table.column>
                    @endif
                    @if($customer->tracker)
                        <x-components::table.column>{{ $customer->tracker->name }}</x-components::table.column>
                    @endif
                    @if($customer->customerSchedules->count())
                        <x-components::table.column>
                            {{ $customer->customerSchedules->count() ? $customer->customerSchedules->unique('time')->map(function ($s) {
                                return substr($s->time, 0, 5);
                            })->sort()->implode(' / ') : '' }}
                        </x-components::table.column>
                    @endif
                </x-components::table.body>
            </x-components::table>
            
            @if($dayReports->count() > 0)
                <x-components::buttons.ainfo primary name="day_reports" :disabled="$dayReports->count() === 0" :href="route('customers.history.index', [$customer])">
                    {{ __('Daily report') }} ({{ $dayReports->count() }})
                </x-components::buttons.ainfo>
            @endif

            @if($monthReports->count() > 0)
                <x-components::buttons.info primary name="monthly_reports" :disabled="$monthReports->count() === 0">
                    {{ __('Monthly reports') }} ({{ $monthReports->count() }})
                    <x-slot name="modalMessage">
                        <h1 class="text-2xl">{{ __('Monthly reports') }}</h1>
                        <x-components::table>
                            <x-components::table.header>
                                <x-components::table.heading>{{ __('Month') }}</x-components::table.heading>
                                <x-components::table.heading></x-components::table.heading>
                            </x-components::table.header>
                            <x-components::table.body>
                                @foreach ($monthReports as $monthReport)
                                    <x-components::table.row :iteration="$loop->iteration">
                                        <x-components::table.column>{{ $monthReport->format('F Y') }}</x-components::table.column>
                                        <x-components::table.column actions>
                                            <x-components::table.action
                                                    :href="route('reports.monthly', [$customer, $monthReport->format('Y'), $monthReport->format('n')])">{{ __('Show report') }}</x-components::table.action>
                                        </x-components::table.column>
                                    </x-components::table.row>
                                @endforeach
                            </x-components::table.body>
                        </x-components::table>
                    </x-slot>
                </x-components::buttons.info>
            @endif

            @if ($weekReports->count() > 0)
                <x-components::buttons.info primary name="weekly_reports" :disabled="$weekReports->count() === 0">
                    {{ __('Weekly reports') }} ({{ $weekReports->count() }})
                    <x-slot name="modalMessage">
                        <h1 class="text-2xl">{{ __('Weekly reports') }}</h1>
                        <x-components::table>
                            <x-components::table.header>
                                <x-components::table.heading>{{ __('Year') }}</x-components::table.heading>
                                <x-components::table.heading>{{ __('Week') }}</x-components::table.heading>
                                <x-components::table.heading></x-components::table.heading>
                            </x-components::table.header>
                            <x-components::table.body>
                                @foreach ($weekReports as $weekReport)
                                    <x-components::table.row>
                                        <x-components::table.column>{{ $weekReport['year'] }}</x-components::table.column>
                                        <x-components::table.column>{{  __('Week') }} {{ $weekReport['week'] }}</x-components::table.column>
                                        <x-components::table.column actions>
                                            <x-components::table.action
                                                    :href="route('reports.weekly', [$customer, $weekReport['year'], $weekReport['week']])">{{ __('Show report') }}</x-components::table.action>
                                        </x-components::table.column>
                                    </x-components::table.row>
                                @endforeach
                            </x-components::table.body>
                        </x-components::table>
                    </x-slot>
                </x-components::buttons.info>
            @endif
            
            @if($verbatimReports->count() > 0)
                <x-components::buttons.ainfo primary name="verbatim_reports" :disabled="$verbatimReports->count() === 0" :href="route('customers.verbatim.index', [$customer])">
                    {{ __('Verbatim report') }} ({{ $verbatimReports->count() }})
                </x-components::buttons.ainfo>
            @endif

            <div class="h-10"></div>
            @include('customers.partials.weeklyScoreGraph')
            <div class="h-10"></div>
            @include('customers.partials.growthDetails')
        </div>
    </div>
</x-app-layout>
