<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">
                {{ __('Tests') }}
            </x-slot>
            <x-slot name="actions">
                @can('create', App\Models\Test::class)
                    @foreach($tests as $index => $test)
                        <x-button.big :outline-white="!current_team()->colorIsLight()"
                                      :outline-black="current_team()->colorIsLight()"
                                      icon="far fa-plus"
                                      href="{{ route('customers.tests.create', [$customer, 'type' => $index]) }}">
                            {{ $test->getName() }}</x-button.big>
                    @endforeach
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    @include('customers.menu')

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Test type') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Date') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @forelse($customer->tests->reverse() as $test)
                <x-components::table.row :iteration="$loop->iteration">
                    <x-components::table.column>{{ $test->data->getName() }}</x-components::table.column>
                    <x-components::table.column>{{ date_format_helper($test->date)->get_dmy() }}</x-components::table.column>
                    <x-components::table.column actions>
                        <x-components::table.action
                                href="{{ route('customers.tests.show', [$customer, $test]) }}">{{ __('Show') }}</x-components::table.action>
                        @can('update', $test)
                            <x-components::table.action
                                    href="{{ route('customers.tests.edit', [$customer, $test]) }}">{{ __('Edit') }}</x-components::table.action>
                        @endcan
                        @can('delete', $test)
                            <x-components::table.action delete
                                                        href="{{ route('customers.tests.destroy', [$customer, $test]) }}">
                                {{ __('Delete') }}
                                <x-slot name="modalMessage">{{ __('Are you sure you want to delete the test?') }}</x-slot>
                            </x-components::table.action>
                        @endcan
                    </x-components::table.column>
                </x-components::table.row>
            @empty
                <x-components::table.row>
                    <x-components::table.column
                            colspan="4">{{ __('No tests yet') }}</x-components::table.column>
                </x-components::table.row>
            @endforelse
        </x-components::table.body>
    </x-components::table>
</x-app-layout>
