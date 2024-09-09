<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Teams') }}
            <x-slot name="suffix">
                {{ __('Create') }}
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

    <div class="px-6">
        @if($errors->any())
            @foreach($errors->all() as $error)
                <x-components::alert.error>{{ $error }}</x-components::alert.error>
            @endforeach
        @endif

        <x-components::form method="post" action="{{ route('teams.store') }}" enctype='multipart/form-data'>
            <x-components::form.fieldset-top>
                <x-components::form.group-top>
                    <x-components::form.label for="company">{{ __('Company') }}</x-components::form.label>
                    <x-components::form.input name="company" id="company" value="{{ old('company') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label for="name">{{ __('Product name') }}</x-components::form.label>
                    <x-components::form.input name="name" id="name" value="{{ old('name') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="settings[reply_to_email]">{{ __('System email address') }}</x-components::form.label>
                    <x-components::form.input name="settings[reply_to_email]"
                                              id="settings[reply_to_email]"
                                              value="{{ old('settings.reply_to_email') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="settings[signature_line]">{{ __('Signature line') }}</x-components::form.label>
                    <x-components::form.input name="settings[signature_line]"
                                              id="settings[signature_line]"
                                              value="{{ old('settings.signature_line') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="settings[color]">{{ __('Header color') }}</x-components::form.label>
                    <x-components::form.input name="settings[color]"
                                              type="color"
                                              id="settings[color]"
                                              placeholder="#ffd603"
                                              value="{{ old('settings.color') ?? '#ffd603' }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="fqdn">{{ __('Domain') }}</x-components::form.label>
                    <x-components::form.input prefix="https://" suffix=".{{ config('app.domain')  }}"
                                              name="fqdn"
                                              id="fqdn" value="{{ old('fqdn') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="timezone">{{ __('Timezone') }}</x-components::form.label>
                    <x-components::form.select name="timezone" id="timezone">
                        @foreach ( timezone_helper()->listAllForSelect() as $group => $values )
                            <x-components::form.select-group label="{{ $group }}">
                                @foreach ( $values as $value )
                                    <x-components::form.select-option
                                            :value="$value['timezone']"
                                            :selected="old('timezone', 'Europe/Amsterdam') == $value['timezone']">{{ str_replace('_', ' ', $value['name']) }}
                                        - ({{ $value['offset_string'] }})
                                    </x-components::form.select-option>
                                @endforeach
                            </x-components::form.select-group>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="plan">{{ __('Plan') }}</x-components::form.label>
                    <x-components::form.select name="plan" id="plan">
                        @foreach ( \Spark\Spark::plans('user') as $plan )
                            <x-components::form.select-option
                                    :value="$plan->id"
                                    :selected="old('plan') == $plan->id">{{ $plan->name }}</x-components::form.select-option>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.checkbox>
                        <x-components::form.checkbox-option name="unlimited_members" id="unlimited_members" value="1">{{ __('Unlimited customers') }} </x-components::form.checkbox-option>
                    </x-components::form.checkbox>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="logo">{{ __('Logo') }}</x-components::form.label>
                    <x-components::form.file name="logo" id="logo"/>
                </x-components::form.group-top>
                <div class="sm:col-span-6">
                    <x-components::dividers.text-center>
                        <span class="text-2xl">{{ __('Admin account') }}</span>
                    </x-components::dividers.text-center>
                </div>
                <x-components::form.group-top-2>
                    <x-components::form.label
                            for="firstname">{{ __('First name') }}</x-components::form.label>
                    <x-components::form.input name="first_name" id="first_name"
                                              value="{{ old('first_name') }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top-2>
                    <x-components::form.label
                            for="lastname">{{ __('Last name') }}</x-components::form.label>
                    <x-components::form.input name="last_name" id="last_name"
                                              value="{{ old('last_name') }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top>
                    <x-components::form.label
                            for="email">{{ __('Email') }}</x-components::form.label>
                    <x-components::form.input name="email" id="email" value="{{ old('email') }}"/>
                </x-components::form.group-top>
            </x-components::form.fieldset-top>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
