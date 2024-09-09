<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Export') }}
            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('exports.index') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="px-6">
        @if($errors->any())
            @foreach($errors->all() as $error)
                <x-components::alert.error>{{ $error }}</x-components::alert.error>
            @endforeach
        @endif

        <x-components::form method="post" action="{{ route('exports.store') }}" enctype='multipart/form-data'>
            <x-components::form.fieldset-top>
                <x-components::form.group-top>
                    <x-components::form.label for="year">{{ __('Year') }}</x-components::form.label>
                    <x-components::form.select name="year">
                        @foreach(range(2016, Illuminate\Support\Carbon::now()->year) as $year)
                            <x-components::form.select-option
                                    value="{{ $year }}"
                                    :selected="$loop->last">{{ $year }}</x-components::form.select-option>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label for="customer">{{ __('Customer') }}</x-components::form.label>
                    <x-components::form.select name="customer">
                        <x-components::form.select-option value=""></x-components::form.select-option>
                        @foreach($customers as $customer)
                            <x-components::form.select-option
                                    value="{{ $customer->id }}">{{ $customer->name }}</x-components::form.select-option>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top>
                <x-components::form.group class="px-0 sm:col-span-6">
                    <x-components::form.checkbox>
                        <x-components::form.checkbox-option name="mail"
                                                            :checked="true"
                                                            value="1">
                            {{ __('Mail me once the export is created.') }}
                        </x-components::form.checkbox-option>
                    </x-components::form.checkbox>
                </x-components::form.group>
            </x-components::form.fieldset-top>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Export') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
