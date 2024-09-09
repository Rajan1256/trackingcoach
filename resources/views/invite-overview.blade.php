<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Overzicht') }} {{ $type }}: {{ $date }}
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Name') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Team') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Time') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Has invite') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Date sent') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Invite status') }}</x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @foreach($customers->sortBy(['auto_invite_time', 'team.id']) as $customer)
                <x-components::table.row>
                    <x-components::table.column>
                        {{ $customer->user->name }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $customer->team?->name }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $customer->auto_invite_time }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $customer->user->invites->count() > 0 ? 'Yes' : 'No' }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $customer->user->invites->first()?->created_at->format('d-m-Y') }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $customer->user->invites->first()?->delivery_status ?? 'Onbekend' }}
                    </x-components::table.column>
                </x-components::table.row>
            @endforeach
        </x-components::table.body>
    </x-components::table>
</x-app-layout>
