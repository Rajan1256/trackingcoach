<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('360 interviews') }}</x-slot>
            <x-slot name="actions">
                @can('create', App\Models\Interview::class)
                    <x-button.big
                            icon="far fa-plus"
                            :href="route('customers.interviews.create', $customer)"
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()">
                        New interview
                    </x-button.big>
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
            <x-components::table.heading>{{ __('Date') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Author') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @forelse($interviews->reverse() as $interview)
                <x-components::table.row :iteration="$loop->iteration">
                    <x-components::table.column>{{ date_format_helper($interview->date)->get_dmy() }}</x-components::table.column>
                    <x-components::table.column>{{ $interview->author->name }}</x-components::table.column>
                    <x-components::table.column class="text-right">
                        <x-components::table.action
                                href="{{ route('customers.interviews.show', [$customer, $interview]) }}">{{ __('Show') }}</x-components::table.action>
                        @can('update', $interview)
                            <x-components::table.action
                                    href="{{ route('customers.interviews.edit', [$customer, $interview]) }}">{{ __('Edit') }}</x-components::table.action>
                        @endcan
                        @can('delete', $interview)
                            <x-components::table.action delete
                                                        href="{{ route('customers.interviews.destroy', [$customer, $interview]) }}"
                                                        firstButton="{{ __('Delete') }}"
                                                        title="{{ __('Delete') }}">
                                {{ __('Delete') }}
                                <x-slot name="modalMessage">
                                    {{ __('Are you sure you want to delete the interview?') }}
                                </x-slot>
                            </x-components::table.action>
                        @endcan
                    </x-components::table.column>
                </x-components::table.row>
            @empty
                <x-components::table.row>
                    <x-components::table.column colspan="3" class="text-center">
                        No interviews yet. <a class="text-blue-700 hover:underline hover:text-blue-800"
                                              href="{{ route('customers.interviews.create', $customer) }}">Create
                            new interview</a>.
                    </x-components::table.column>
                </x-components::table.row>
            @endforelse
        </x-components::table.body>
    </x-components::table>
</x-app-layout>
