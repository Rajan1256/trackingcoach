<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">
                {{ __('Timeouts for :month', ['month' => date_format_helper($start)->get_full_month_and_year()]) }}
            </x-slot>
            <x-slot name="actions">
                <x-button.big
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()"
                        href="{{ route('customers.timeouts.index', [$customer, 'year' => $prev->year, 'month' => $prev->month]) }}
                                ">
                    <i class="fas fa-chevron-left"></i></x-button.big>
                <x-button.big
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()"
                        href="{{ route('customers.timeouts.index', [$customer, 'year' => date('Y'), 'month' => date('n')]) }}
                                ">
                    {{ __('Current') }}</x-button.big>
                <x-button.big
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()"
                        href="{{ route('customers.timeouts.index', [$customer, 'year' => $next->year, 'month' => $next->month]) }}
                                ">
                    <i class="fas fa-chevron-right"></i></x-button.big>
                @can('create', App\Models\Timeout::class)
                    <x-button.big
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()"
                            href="{{ route('customers.timeouts.create', [$customer]) }}">{{ __('New timeout') }}</x-button.big>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    @include('customers.menu')
    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Start') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('End') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        @forelse($timeouts as $timeout)
            <x-components::table.row :iteration="$loop->iteration">
                <x-components::table.column>{{ date_format_helper($timeout->start)->get_dmy() }}</x-components::table.column>
                <x-components::table.column>{{ date_format_helper($timeout->end)->get_dmy() }}</x-components::table.column>
                <x-components::table.column actions>
                    @can('update', $timeout)
                        <x-components::table.action
                                href="{{ route('customers.timeouts.edit', [$customer, $timeout]) }}">{{ __('Edit') }}</x-components::table.action>
                    @endcan
                    @can('delete', $timeout)
                        <x-components::table.action delete
                                                    href="{{ route('customers.timeouts.destroy', [$customer, $timeout]) }}">
                            {{ __('Delete') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to delete the timeout?') }}</x-slot>
                        </x-components::table.action>
                    @endcan
                </x-components::table.column>
            </x-components::table.row>
        @empty
            <x-components::table.row>
                <x-components::table.column
                        colspan="3"
                        class="text-center italic">{{ __('No timeouts for this month.') }}</x-components::table.column>
            </x-components::table.row>
        @endforelse
    </x-components::table>
</x-app-layout>
