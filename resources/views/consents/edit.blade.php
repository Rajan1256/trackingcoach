<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Consents') }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('consents.index') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="px-6">
        <x-components::form method="post" action="{{ route('consents.update', [$consent]) }}"
                            enctype="multipart/form-data">
            <x-slot name="customMethod">@method('PUT')</x-slot>
            <x-components::form.fieldset>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Name') }}</x-components::form.label>
                    <x-components::form.input name="name" value="{{ $consent->name }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Description') }}</x-components::form.label>
                    <x-components::form.textarea
                            name="description">{{ $consent->description }}</x-components::form.textarea>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Confirmation text') }}</x-components::form.label>
                    <x-components::form.input name="confirmation_text" value="{{ $consent->confirmation_text }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label optional>{{ __('Attachment (PDF)') }}</x-components::form.label>
                    <x-components::form.file name="file"/>
                </x-components::form.group-top>
                @if (current_team()->isRoot())
                    <x-components::form.group>
                        <x-components::form.checkbox>
                            <x-components::form.checkbox-option name="global" value="1"
                                                                :checked="is_null($consent->team_id)">
                                {{ __('Global consent') }}
                            </x-components::form.checkbox-option>
                        </x-components::form.checkbox>
                    </x-components::form.group>
                @endif
            </x-components::form.fieldset>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
