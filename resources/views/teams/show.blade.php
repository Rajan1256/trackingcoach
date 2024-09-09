<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Teams') }}
            <x-slot name="suffix">
                {{ $team->name }}
            </x-slot>
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('teams.index') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="p-6 bg-white">
        <x-components::grid cols="2">
            <x-components::grid.block>
                <x-components::table>
                    <x-components::table.body>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Company') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->company }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Product name') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->name }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('URL') }}</x-components::table.column>
                            <x-components::table.column>
                                @if($team->trashed())
                                    <span class="line-through">{{ $team->fqdn }}</span>
                                @else
                                    <a class="underline text-blue-600"
                                       href="//{{ $team->fqdn }}">{{ $team->fqdn }}</a>
                                @endif
                            </x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('System email adress') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->settings['reply_to_email'] }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Signature line') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->settings['signature_line'] ?? '' }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Timezone') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->timezone }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Logo') }}</x-components::table.column>
                            <x-components::table.column>
                                @if($team->logo)
                                    <img style="max-height: 40px;" src="{{ $team->logo }}">
                                @else
                                    <em>Default</em>
                                @endif
                            </x-components::table.column>
                        </x-components::table.row>
                    </x-components::table.body>
                </x-components::table>
            </x-components::grid.block>
            <x-components::grid.block>
                <x-components::table>
                    <x-components::table.body>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Created at') }}</x-components::table.column>
                            <x-components::table.column>{{ date_format_helper($team->created_at)->get_dmy() }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Customers') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->users()->isCustomer()->count() }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Coaches') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->users()->isCoach()->count() }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Administrators') }}</x-components::table.column>
                            <x-components::table.column>{{ $team->users()->isAdministrator()->count() }}</x-components::table.column>
                        </x-components::table.row>
                        <x-components::table.row>
                            <x-components::table.column
                                    class="font-bold">{{ __('Unlimited customers') }}</x-components::table.column>
                            <x-components::table.column>
                                @if($team->unlimited_members)
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-times"></i>
                                @endif
                            </x-components::table.column>
                        </x-components::table.row>
                    </x-components::table.body>
                </x-components::table>
                <x-components::buttons class="items-center">
                    @if (!Auth::user()->belongsToTeam($team))
                        <x-components::buttons.confirm class="bg-blue-500 text-white hover:bg-blue-600 rounded-md ml-2"
                                                       href="{{ route('teams.assign', [$team]) }}"
                                                       :disabled="Auth::user()->belongsToTeam($team)"
                                                       name="restore">
                            {{ __('Assign to team') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to assign yourself to the team?') }}</x-slot>
                        </x-components::buttons.confirm>
                    @endif
                    @if(Auth::user()->belongsToTeam($team) && $team->owner->id !== Auth::user()->id)
                        <x-components::buttons.delete class="bg-blue-500 text-white hover:bg-blue-600 rounded-md ml-2"
                                                      href="{{ route('teams.unassign', [$team]) }}"
                                                      method="DELETE"
                                                      :disabled="!Auth::user()->belongsToTeam($team) || $team->owner->id === Auth::user()->id"
                                                      name="restore">
                            {{ __('Unassign from team') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to unassign yourself from the team?') }}</x-slot>
                        </x-components::buttons.delete>
                    @elseif($team->owner->id === Auth::user()->id)
                        {{ __('You are the owner of this team') }}
                    @endif
                    @if(!$team->trashed())
                        <x-components::buttons.delete class="bg-red-500 text-white hover:bg-red-600 rounded-md ml-2"
                                                      href="{{ route('teams.destroy', [$team]) }}"
                                                      name="destroy">
                            {{ __('Deactivate') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to deactivate the team?') }}</x-slot>
                        </x-components::buttons.delete>
                    @else
                        <x-components::buttons.confirm class="bg-blue-500 text-white hover:bg-blue-600 rounded-md ml-2"
                                                       href="{{ route('teams.restore', [$team]) }}"
                                                       method="PUT"
                                                       name="restore">
                            {{ __('Restore') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to restore this team?') }}</x-slot>
                        </x-components::buttons.confirm>
                        <x-components::buttons.delete class="bg-red-500 text-white hover:bg-red-600 rounded-md ml-2"
                                                      href="{{ route('teams.permanently', [$team]) }}"
                                                      name="permanently">
                            {{ __('Permanently destroy') }}
                            <x-slot name="modalMessage">{{ __('Are you sure you want to permanently delete the team? This cannot be undone!') }}</x-slot>
                        </x-components::buttons.delete>
                    @endif
                </x-components::buttons>
            </x-components::grid.block>
        </x-components::grid>
    </div>
</x-app-layout>
