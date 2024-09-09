<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('History') }}</x-slot>
        </x-page-header>
    </x-slot>

    @include('customers.menu')
    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Date') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Score') }}</x-components::table.heading>
            <x-components::table.heading># {{ __('Answers') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Time') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @forelse($dates as $date)
                <x-components::table.row :iteration="$loop->iteration">
                    <x-components::table.column>{{ $date->date->format('d-m-Y') }}</x-components::table.column>
                    <x-components::table.column>
                        <x-components::badges.basic
                                :color="scoreToColorClass($date->score)">
                            {{ $date->score }}
                        </x-components::badges.basic>
                    </x-components::table.column>
                    <x-components::table.column>{{ $date->count }}</x-components::table.column>
                    <x-components::table.column>
                        {{ $date->created_at->format('H:i') }}
                        @if($date->created_at->format('d') !== $date->date->format('d'))
                            (+{{ $date->created_at->diffInDays($date->date) }} {{ __('days') }})
                        @endif
                    </x-components::table.column>
                    <x-components::table.column>
                        <x-components::table.action
                                href="{{ route('customers.history.show', [$customer, $date->date->format('Y-m-d')]) }}">
                            {{ __('Show') }}
                        </x-components::table.action>
                    </x-components::table.column>
                </x-components::table.row>
            @empty
                <x-components::table.row>
                    <x-components::table.column
                            colspan="5">{{ __('There is no history for this client (yet)') }}</x-components::table.column>
                </x-components::table.row>
            @endforelse
        </x-components::table.body>
    </x-components::table>
    <div class="p-6">
        {{ $dates->links() }}
    </div>
</x-app-layout>
