<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Account settings') }}
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
        <x-components::form method="post" action="{{ route('settings.update') }}">
            <x-components::form.fieldset-top>
                <x-components::form.group-top-2>
                    <x-components::form.label for="first_name">
                        {{ __('First name') }}
                    </x-components::form.label>
                    <x-components::form.input name="first_name" id="first_name"
                                              value="{{ old('first_name', isset($settings) ? $settings->first_name : '') }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top-2>
                    <x-components::form.label for="last_name">
                        {{ __('Last name') }}
                    </x-components::form.label>
                    <x-components::form.input name="last_name" id="last_name"
                                              value="{{ old('last_name', isset($settings) ? $settings->last_name : '') }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top>
                    <x-components::form.label for="email">
                        {{ __('Email') }}
                    </x-components::form.label>
                    <x-components::form.input name="email" id="email"
                                              value="{{ old('email', isset($settings) ? $settings->email : '') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label for="phone">
                        {{ __('Phone') }}
                    </x-components::form.label>
                    <x-components::form.input name="phone" id="phone"
                                              value="{{ old('phone', isset($settings) ? $settings->phone : '') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top-3>
                    <x-components::form.label>{{ __('Language') }}</x-components::form.label>
                    <x-components::form.select name="locale">
                        @foreach(config('trackingcoach.languages') as $short => $full)
                            <x-components::form.select-option
                                    value="{{ $short }}"
                                    :selected="old('locale', isset($settings) ? $settings->locale : '') == $short">
                                {{ __($full) }}
                            </x-components::form.select-option>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top-3>
                <x-components::form.group-top-3>
                    <x-components::form.label>{{ __('Date format') }}</x-components::form.label>
                    <x-components::form.select name="date_format">
                        @foreach ( \App\Modules\Dates\DateFormat::get_database_values_with_example() as $key => $value)
                            <x-components::form.select-option
                                    :value="$key"
                                    :selected="old('date_format', isset($settings) ? $settings->date_format : '') == $key">
                                {{ $value }}
                            </x-components::form.select-option>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top-3>
                <x-components::form.group-top-3>
                    <x-components::form.label>{{ __('Timezone') }}</x-components::form.label>
                    <x-components::form.select name="timezone">
                        @foreach ( timezone_helper()->listAllForSelect() as $group => $values )
                            <x-components::form.select-group label="{{ $group }}">
                                @foreach ( $values as $value )
                                    <x-components::form.select-option
                                            :value="$value['timezone']"
                                            :selected="old('timezone', isset($settings) ? $settings->timezone : 'Europe/Amsterdam') == $value['timezone']">{{ str_replace('_', ' ', $value['name']) }}
                                        - ({{ $value['offset_string'] }})
                                    </x-components::form.select-option>
                                @endforeach
                            </x-components::form.select-group>
                        @endforeach
                    </x-components::form.select>
                </x-components::form.group-top-3>
                <x-components::form.group-top>
                    <x-components::form.label for="company_name">
                        {{ __('Company name') }}
                    </x-components::form.label>
                    <x-components::form.input name="company_name" id="company_name"
                                              value="{{ old('company_name', isset($settings) ? $settings->company_name : '') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top-3>
                    <x-components::form.label for="preferred_notification_methods[daily_invites]">
                        {{ __('Daily invites') }}
                    </x-components::form.label>
                    <x-components::form.radio>
                        <x-components::form.radio-option
                                value="app"
                                id="preferred_notification_methods[daily_invites]app"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'app'">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                id="preferred_notification_methods[daily_invites]mail"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'mail'">{{ __('Email') }}</x-components::form.radio-option>
                        <!-- <x-components::form.radio-option
                                value="sms"
                                id="preferred_notification_methods[daily_invites]sms"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                id="preferred_notification_methods[daily_invites]both"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'both'">{{ __('SMS & Email') }}</x-components::form.radio-option> -->
                    </x-components::form.radio>
                </x-components::form.group-top-3>
                <x-components::form.group-top-3>
                    <x-components::form.label for="preferred_notification_methods[weekly_reports]">
                        {{ __('Weekly reports') }}
                    </x-components::form.label>
                    <x-components::form.radio>
                        <x-components::form.radio-option
                                value="app"
                                id="preferred_notification_methods[weekly_reports]app"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'app'">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                id="preferred_notification_methods[weekly_reports]mail"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'mail'">{{ __('Email') }}</x-components::form.radio-option>
                        <!-- <x-components::form.radio-option
                                value="sms"
                                id="preferred_notification_methods[weekly_reports]sms"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                id="preferred_notification_methods[weekly_reports]both"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'both'">{{ __('SMS & Email') }}</x-components::form.radio-option> -->
                    </x-components::form.radio>
                </x-components::form.group-top-3>
                <x-components::form.group-top-3>
                    <x-components::form.label for="preferred_notification_methods[monthly_reports]">
                        {{ __('Monthly reports') }}
                    </x-components::form.label>
                    <x-components::form.radio>
                        <x-components::form.radio-option
                                value="app"
                                id="preferred_notification_methods[monthly_reports]app"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'app'">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                id="preferred_notification_methods[monthly_reports]mail"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'mail'">{{ __('Email') }}</x-components::form.radio-option>
                        <!-- <x-components::form.radio-option
                                value="sms"
                                id="preferred_notification_methods[monthly_reports]sms"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                id="preferred_notification_methods[monthly_reports]both"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'both'">{{ __('SMS & Email') }}</x-components::form.radio-option> -->
                    </x-components::form.radio>
                </x-components::form.group-top-3>
                <div class="sm:col-span-6 mt-3">
                    <x-components::dividers.text-center>
                        <span class="text-xl">{{ __('Change password') }}</span>
                    </x-components::dividers.text-center>
                </div>
                <x-components::form.group-top-2>
                    <x-components::form.label for="password">
                        {{ __('Password') }}
                    </x-components::form.label>
                    <x-components::form.input name="password" id="password" type="password"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top-2>
                    <x-components::form.label for="password_confirmation">
                        {{ __('Confirm password') }}
                    </x-components::form.label>
                    <x-components::form.input name="password_confirmation" id="password_confirmation" type="password"/>
                </x-components::form.group-top-2>
            </x-components::form.fieldset-top>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
