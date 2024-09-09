<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Consents') }}

            <x-slot name="actions">
                @can('create', App\Models\Consent::class)
                    <x-button.big
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()"
                            href="{{ route('consents.create') }}">{{ __('New consent') }}</x-button.big>
                @endcan
            </x-slot>
        </x-page-header>

    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Name') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Active') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('# Users') }}</x-components::table.heading>
            @if(current_team()->isRoot())
                <x-components::table.heading>{{ __('Global') }}</x-components::table.heading>
            @endif
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        @forelse($consents as $consent)
            <x-components::table.row :iteration="$loop->iteration">
                <x-components::table.column>{{ $consent->name }}</x-components::table.column>
                <x-components::table.column>
                    @if($consent->isActive())
                        <i class="fas fa-check"></i>
                    @else
                        <i class="fas fa-times"></i>
                    @endif
                </x-components::table.column>
                <x-components::table.column>{{ $consent->users()->count() }}</x-components::table.column>
                @if(current_team()->isRoot())
                    <x-components::table.column>
                        @if($consent->team_id === null)
                            <i class="fas fa-check"></i>
                        @else
                            <i class="fas fa-times"></i>
                        @endif
                    </x-components::table.column>
                @endif
                <x-components::table.column actions>
                    <x-components::table.action
                            href="{{ route('consents.show', [$consent]) }}">{{ __('Preview') }}</x-components::table.action>
                    <x-components::table.action
                            href="{{ route('consents.edit', [$consent]) }}">{{ __('Edit') }}</x-components::table.action>

                    @if($consent->isActive())
                        <x-components::table.action confirm
                                                    href="{{ route('consents.deactivate', [$consent]) }}">
                            {{ __('Deactivate') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to deactivate the consent?') }}</x-slot>
                        </x-components::table.action>
                    @else
                        <x-components::table.action confirm
                                                    href="{{ route('consents.activate', [$consent]) }}">
                            {{ __('Activate') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to activate the consent?') }}</x-slot>
                        </x-components::table.action>
                    @endif

                    <x-components::table.action delete
                                                href="{{ route('consents.destroy', [$consent]) }}">
                        {{ __('Delete') }}
                        <x-slot name="modalMessage">{{ __('Are you sure you want to delete the consent?') }}</x-slot>
                    </x-components::table.action>
                </x-components::table.column>
            </x-components::table.row>
        @empty
            <x-components::table.row>
                <x-components::table.column
                        colspan="4" class="text-center italic">
                    {{ __('No consents yet') }}
                </x-components::table.column>
            </x-components::table.row>
        @endforelse
    </x-components::table>
</x-app-layout>
