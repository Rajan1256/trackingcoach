<x-app-layout>
    <x-slot name="hideNavigation"></x-slot>
    <x-slot name="header">
        <x-page-header>
            {{ __('Create your account') }}
        </x-page-header>
    </x-slot>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="p-6">
        <x-components::form method="post" action="{{ route('accept.invite.store', $invite->token) }}">
            <x-components::form.fieldset-top>
                <x-components::form.group-top-2>
                    <x-components::form.label for="first_name">
                        {{ __('First name') }}
                    </x-components::form.label>
                    <x-components::form.input name="first_name" id="first_name"
                                              value="{{ $first_name }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top-2>
                    <x-components::form.label for="last_name">
                        {{ __('Last name') }}
                    </x-components::form.label>
                    <x-components::form.input name="last_name" id="last_name"
                                              value="{{ $last_name }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top>
                    <x-components::form.label for="email">
                        {{ __('Email') }}
                    </x-components::form.label>
                    <x-components::form.input disabled class="disabled:bg-gray-100" name="email" id="email"
                                              value="{{ $email }}"/>
                </x-components::form.group-top>
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
                <x-components::form.group-top>
                    <x-components::form.label for="phone">
                        {{ __('Phone') }}
                    </x-components::form.label>
                    <x-components::form.input name="phone" id="phone"
                                              value="{{ old('phone', $invite->data['phone'] ?? '') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top-3>
                    <x-components::form.label>{{ __('Language') }}</x-components::form.label>
                    <x-components::form.select name="locale">
                        @foreach(config('trackingcoach.languages') as $short => $full)
                            <x-components::form.select-option
                                    value="{{ $short }}"
                                    :selected="old('locale', $invite->data['locale'] ?? '') == $short">
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
                                    :selected="old('date_format', $invite->data['date_format'] ?? '') == $key">
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
                                            :selected="old('timezone', $invite->data['timezone'] ?? 'Europe/Amsterdam') == $value['timezone']">{{ str_replace('_', ' ', $value['name']) }}
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
                                              value="{{ old('company_name', $invite->data['company_name'] ?? '') }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top-3>
                    <x-components::form.label for="preferred_notification_methods[daily_invites]">
                        {{ __('Daily invites') }}
                    </x-components::form.label>
                    <x-components::form.radio>
                        <x-components::form.radio-option
                                value="app"
                                id="preferred_notification_methods[daily_invites]app"
                                name="preferred_notification_methods[daily_invites]">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                id="preferred_notification_methods[daily_invites]mail"
                                name="preferred_notification_methods[daily_invites]">{{ __('Email') }}</x-components::form.radio-option>
                        <!-- <x-components::form.radio-option
                                value="sms"
                                id="preferred_notification_methods[daily_invites]sms"
                                name="preferred_notification_methods[daily_invites]">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                id="preferred_notification_methods[daily_invites]both"
                                name="preferred_notification_methods[daily_invites]">{{ __('SMS & Email') }}</x-components::form.radio-option> -->
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
                                name="preferred_notification_methods[weekly_reports]">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                id="preferred_notification_methods[weekly_reports]mail"
                                name="preferred_notification_methods[weekly_reports]">{{ __('Email') }}</x-components::form.radio-option>
                        <!-- <x-components::form.radio-option
                                value="sms"
                                id="preferred_notification_methods[weekly_reports]sms"
                                name="preferred_notification_methods[weekly_reports]">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                id="preferred_notification_methods[weekly_reports]both"
                                name="preferred_notification_methods[weekly_reports]">{{ __('SMS & Email') }}</x-components::form.radio-option> -->
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
                                name="preferred_notification_methods[monthly_reports]">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                id="preferred_notification_methods[monthly_reports]mail"
                                name="preferred_notification_methods[monthly_reports]">{{ __('Email') }}</x-components::form.radio-option>
                        <!-- <x-components::form.radio-option
                                value="sms"
                                id="preferred_notification_methods[monthly_reports]sms"
                                name="preferred_notification_methods[monthly_reports]">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                id="preferred_notification_methods[monthly_reports]both"
                                name="preferred_notification_methods[monthly_reports]">{{ __('SMS & Email') }}</x-components::form.radio-option> -->
                    </x-components::form.radio>
                    !
                </x-components::form.group-top-3>
            </x-components::form.fieldset-top>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
