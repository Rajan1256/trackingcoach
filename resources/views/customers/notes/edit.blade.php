<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Edit note') }}</x-slot>
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('customers.notes.index', [$customer]) }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif


    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="px-6 bg-white border-b border-gray-200">
            <x-components::form action="{{ route('customers.notes.update', [$customer, $note]) }}"
                                method="post">
                <x-slot name="customMethod">
                    @method('PUT')
                </x-slot>
                <x-components::form.fieldset>
                    <x-components::form.group-top>
                        <x-components::form.textarea
                                rows="20"
                                required
                                placeholder="{{ __('Type a new note here') }}"
                                name="note">{{ $note->body }}</x-components::form.textarea>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        <x-components::form.label>{{ __('Authorized to read') }}</x-components::form.label>
                        <x-components::form.select name="authorization">
                            <x-components::form.select-option
                                    value="1"
                                    :selected="$note->authorization === 1">
                                @if($customer->id === Auth::user()->id)
                                    {{ __('Coach and me') }}
                                @else
                                    {{ __('Client and me') }}
                                @endif
                            </x-components::form.select-option>
                            <x-components::form.select-option
                                    value="2"
                                    :selected="$note->authorization === 2">{{ __('Just me') }}</x-components::form.select-option>
                        </x-components::form.select>
                    </x-components::form.group-top>
                </x-components::form.fieldset>
                <x-components::form.button-group>
                    <x-components::form.button submit
                                               primary>{{ __('Save note') }}</x-components::form.button>
                </x-components::form.button-group>
            </x-components::form>
        </div>
    </div>
</x-app-layout>
