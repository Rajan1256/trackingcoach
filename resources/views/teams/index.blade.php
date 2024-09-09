<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Teams') }}
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-plus"
                              href="{{ route('teams.create') }}">
                    {{ __('Create new team') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <x-components::table>
        <x-components::table.header>
            <x-components::table.heading>{{ __('Company') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Product name') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Plan') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Subscription status') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Customers') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Unlimited customers') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Created at') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('Link') }}</x-components::table.heading>
            <x-components::table.heading>{{ __('CB') }}</x-components::table.heading>
            <x-components::table.heading></x-components::table.heading>
        </x-components::table.header>
        <x-components::table.body>
            @foreach($teams as $team)
                <x-components::table.row>
                    <x-components::table.column
                            :class="($team->trashed()) ? 'line-through' : ''">{{ $team->company }}</x-components::table.column>
                    <x-components::table.column
                            :class="($team->trashed()) ? 'line-through' : ''">{{ $team->name }}</x-components::table.column>
                    <x-components::table.column
                            :class="($team->trashed()) ? 'line-through' : ''">{{ $team->stripePlanName() }}</x-components::table.column>
                    <x-components::table.column
                            :class="($team->trashed()) ? 'line-through' : ''">
                        @if ($team->onTrial())
                            {{ __('Trial') }}
                        @elseif ($team->subscribed())
                            {{ __('Subscribed') }}
                        @else
                            {{ __('Not subscribed') }}
                        @endif
                    </x-components::table.column>
                    <x-components::table.column
                            :class="($team->trashed()) ? 'line-through' : ''">{{ $team->users()->isCustomer()->count() }}</x-components::table.column>
                    <x-components::table.column>
                        @if($team->unlimited_members)
                            <i class="fas fa-check"></i>
                        @else
                            <i class="fas fa-times"></i>
                        @endif
                    </x-components::table.column>
                    <x-components::table.column
                            :class="($team->trashed()) ? 'line-through' : ''">{{ date_format_helper($team->created_at)->get_dmy() }}</x-components::table.column>
                    <x-components::table.column>
                        @if(!$team->trashed())
                            <x-components::table.action
                                    href="//{{ $team->fqdn }}"><i class="fas fa-globe-europe"></i>
                            </x-components::table.action>
                        @endif
                    </x-components::table.column>
                    <x-components::table.column>
                        @if($team->getFirstMedia('logos'))
                            <i class="fas fa-check"></i>
                        @else
                            <i class="fas fa-times"></i>
                        @endif
                    </x-components::table.column>
                    <x-components::table.column>
                        <x-components::table.action
                                href="{{ route('teams.show', [$team]) }}">{{ __('Show') }}</x-components::table.action>
                        <x-components::table.action
                                href="{{ route('teams.edit', [$team]) }}">{{ __('Edit') }}</x-components::table.action>
                    </x-components::table.column>
                </x-components::table.row>
            @endforeach
        </x-components::table.body>
    </x-components::table>
    <div class="p-6">
        {{ $teams->links() }}
    </div>
</x-app-layout>
