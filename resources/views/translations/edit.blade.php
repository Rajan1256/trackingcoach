<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Translations') }}
            <x-slot name="suffix">
                {{ __('Edit') }}
            </x-slot>
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('translations.index') }}">
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

    <div class="px-6 pt-6">
        {{ __('Variables are indicated with a : in front of them. Please do not translate these.') }}
        <x-components::form method="post" action="{{ route('translations.update', $line) }}">
            <x-slot name="customMethod">
                @method('PUT')
            </x-slot>
            <x-components::form.fieldset>
                @foreach(config('trackingcoach.languages') as $short => $full)
                    <x-components::form.group-top>
                        <x-components::form.label>{{ __($full) }}</x-components::form.label>
                        <x-components::form.textarea
                                name="text[{{ $short }}]">{{ $line->getTranslation($short) }}</x-components::form.textarea>
                    </x-components::form.group-top>
                @endforeach
            </x-components::form.fieldset>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Update') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
