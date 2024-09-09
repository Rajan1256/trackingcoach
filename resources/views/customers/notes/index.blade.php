<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Notes') }}</x-slot>
            <x-slot name="actions">
                @can('create', \App\Models\Note::class)
                    <x-button.big :outline-white="!current_team()->colorIsLight()"
                                  :outline-black="current_team()->colorIsLight()"
                                  icon="far fa-plus"
                                  href="{{ route('customers.notes.create', [$customer]) }}">
                        {{ __('Create a note') }}</x-button.big>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">

            <ul role="list" class="space-y-8">
                @foreach ($notes as $note)
                    <li>
                        <div class="flex space-x-3">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full"
                                     src="{{ \Creativeorange\Gravatar\Facades\Gravatar::get($note->author->email) }}"
                                     alt="">
                            </div>
                            <div>
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $note->author->name }}</span>
                                </div>
                                <div class="mt-1 text-sm text-gray-700">
                                    <p>{!! nl2br($note->body) !!}</p>
                                </div>
                                <div class="mt-2 text-sm space-x-2">
                                    <span class="text-gray-500 font-medium">{{ date_format_helper($note->created_at)->get_dmy() }}</span>
                                    <span class="text-gray-500 font-medium">&middot;</span>
                                    @can('update', $note)
                                        <x-components::lists.action
                                                class="mr-2"
                                                href="{{ route('customers.notes.edit', [$customer, $note]) }}">{{ __('Edit') }}</x-components::lists.action>
                                    @endcan
                                    @can('delete', $note)
                                        <x-components::lists.action delete
                                                                    name="delete_{{ $note->id }}"
                                                                    firstButton="{{ __('Delete') }}"
                                                                    title="{{ __('Delete') }}"
                                                                    href="{{ route('customers.notes.destroy', [$customer, $note]) }}">
                                            {{ __('Delete') }}
                                            <x-slot name="modalMessage">
                                                {{ __('Are you sure you want to delete the note?') }}
                                            </x-slot>
                                        </x-components::lists.action>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-app-layout>
