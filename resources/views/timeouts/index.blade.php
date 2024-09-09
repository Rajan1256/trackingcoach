<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Timeouts') }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              href="{{ route('timeouts.index', ['year' => $prev->year, 'month' => $prev->month]) }}">
                    <i class="fas fa-chevron-left"></i></x-button.big>
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              href="{{ route('timeouts.index', ['year' => date('Y'), 'month' => date('n')]) }}">
                    {{ __('Current') }}</x-button.big>
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              href="{{ route('timeouts.index', ['year' => $next->year, 'month' => $next->month]) }}">
                    <i class="fas fa-chevron-right"></i></x-button.big>
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              href="{{ route('timeouts.create') }}">{{ __('New timeout') }}</x-button.big>

            </x-slot>
        </x-page-header>

    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Customer') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Start') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('End') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        @forelse($timeouts as $timeout)
            <x-components::table.row>
                <x-components::table.column>{{ $timeout->user->name }}</x-components::table.column>
                <x-components::table.column>{{ date_format_helper($timeout->start)->get_dmy() }}</x-components::table.column>
                <x-components::table.column>{{ date_format_helper($timeout->end)->get_dmy() }}</x-components::table.column>
                <x-components::table.column actions>
                    <x-components::table.action
                            href="{{ route('timeouts.edit', [$timeout]) }}">{{ __('Edit') }}</x-components::table.action>
                    <x-components::table.action delete
                                                href="{{ route('timeouts.destroy', [$timeout]) }}">
                        {{ __('Delete') }}
                        <x-slot name="modalMessage">{{ __('Are you sure you want to delete the timeout?') }}</x-slot>
                    </x-components::table.action>
                </x-components::table.column>
            </x-components::table.row>
        @empty
            <x-components::table.row>
                <x-components::table.column
                        colspan="4" class="text-center italic">
                    No timeouts for this period
                </x-components::table.column>
            </x-components::table.row>
        @endforelse
    </x-components::table>
</x-app-layout>
