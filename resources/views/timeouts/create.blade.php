<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Timeouts') }}
            <x-slot name="suffix">
                {{ __('Create') }}
            </x-slot>
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('timeouts.index') }}">
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
        <x-components::form method="post" action="{{ route('timeouts.store') }}">
            <x-components::form.fieldset>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Customer') }}</x-components::form.label>
                    <x-components::form.select id="customer" name="customer">
                        <x-components::form.select-option value="0"></x-components::form.select-option>
                        @foreach ($customers as $customer)
                            <x-components::form.select-option value="{{ $customer->id }}">
                                {{ $customer->name }}
                            </x-components::form.select-option>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Start') }}</x-components::form.label>
                    <x-components::form.date name="start"></x-components::form.date>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('End') }}</x-components::form.label>
                    <x-components::form.date name="end"></x-components::form.date>
                </x-components::form.group-top>
            </x-components::form.fieldset>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Create') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
