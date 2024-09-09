<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Teammates') }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              href="{{ route('teams.members.create') }}">
                    {{ __('New team member') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session()->has('message'))
                <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
            @endif

            <x-components::list cards>
                @foreach($members->sortBy('first_name') as $member)
                    @php
                        $href = '';
                        if (\Illuminate\Support\Facades\Route::has('letmein')) {
                            $href = "<a href='" . route('letmein', $member) ."'><i class='fas fa-lock-open-alt'></i></a>";
                        }
                    @endphp
                    <x-components::lists.card-with-2-buttons :header="$member->name"
                                                             :subHeader="(config('app.env') === 'local') ? $href : ''"
                                                             :img="$member->gravatar"
                                                             href="{{ route('teams.members.show', [$member->id]) }}">
                        @can('promote-teamMember', $member)
                            <x-slot name="firstButton">
                                <x-components::lists.button
                                        :disabled="current_team()->owner->id === $member->id"
                                        method="PUT" confirm
                                        href="{{ route('teams.members.promote', [$member->id]) }}">
                                    <i class="fas fa-user-crown" title="{{ __('Promote user to owner') }}"></i>
                                    <x-slot name="modalMessage">{{ __('Are you sure you want to promote the user to owner?') }}</x-slot>
                                </x-components::lists.button>
                            </x-slot>
                        @endcan
                        @can('delete-teamMember', $member)
                            <x-slot name="secondButton">
                                <x-components::lists.button
                                        :disabled="current_team()->owner->id === $member->id"
                                        delete href="{{ route('teams.members.destroy', [$member->id]) }}">
                                    <i class="fas fa-user-times" title="{{ __('Remove user from team') }}"></i>
                                    <x-slot name="modalMessage">{{ __('Are you sure you want to remove the user from the team?') }}</x-slot>
                                </x-components::lists.button>
                            </x-slot>
                        @endcan
                    </x-components::lists.card-with-2-buttons>
                @endforeach
            </x-components::list>
        </div>
    </div>
</x-app-layout>
