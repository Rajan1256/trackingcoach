<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Team member settings: :name', ['name' => $settings->first_name . ' ' . $settings->last_name]) }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('teams.members') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="px-6 bg-white border-b border-gray-200">
        <x-components::form method="post"
                            action="{{ route('teams.members.update', [$settings->user]) }}">
            <x-slot name="customMethod">@method('PUT')</x-slot>
            <x-components::form.fieldset-top>
                <x-components::form.group-top-2>
                    <x-components::form.label for="first_name">
                        {{ __('First name') }}
                    </x-components::form.label>
                    <x-components::form.input name="first_name" id="first_name" disabled
                                              class="disabled:bg-gray-100"
                                              value="{{ old('first_name', isset($settings) ? $settings->first_name : '') }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top-2>
                    <x-components::form.label for="last_name">
                        {{ __('Last name') }}
                    </x-components::form.label>
                    <x-components::form.input name="last_name" id="last_name" disabled
                                              class="disabled:bg-gray-100"
                                              value="{{ old('last_name', isset($settings) ? $settings->last_name : '') }}"/>
                </x-components::form.group-top-2>
                <x-components::form.group-top>
                    <x-components::form.label for="email">
                        {{ __('Email') }}
                    </x-components::form.label>
                    <x-components::form.input name="email" id="email" disabled class="disabled:bg-gray-100"
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
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'app'">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'mail'">{{ __('Email') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="sms"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                name="preferred_notification_methods[daily_invites]"
                                :checked="$settings->preferred_notification_methods['daily_invites'] === 'both'">{{ __('SMS & Email') }}</x-components::form.radio-option>
                    </x-components::form.radio>
                </x-components::form.group-top-3>
                <x-components::form.group-top-3>
                    <x-components::form.label for="preferred_notification_methods[weekly_reports]">
                        {{ __('Weekly reports') }}
                    </x-components::form.label>
                    <x-components::form.radio>
                        <x-components::form.radio-option
                                value="app"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'app'">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'mail'">{{ __('Email') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="sms"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                name="preferred_notification_methods[weekly_reports]"
                                :checked="$settings->preferred_notification_methods['weekly_reports'] === 'both'">{{ __('SMS & Email') }}</x-components::form.radio-option>
                    </x-components::form.radio>
                </x-components::form.group-top-3>
                <x-components::form.group-top-3>
                    <x-components::form.label for="preferred_notification_methods[monthly_reports]">
                        {{ __('Monthly reports') }}
                    </x-components::form.label>
                    <x-components::form.radio>
                        <x-components::form.radio-option
                                value="app"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'app'">{{ __('App notification') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="mail"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'mail'">{{ __('Email') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="sms"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                        <x-components::form.radio-option
                                value="both"
                                name="preferred_notification_methods[monthly_reports]"
                                :checked="$settings->preferred_notification_methods['monthly_reports'] === 'both'">{{ __('SMS & Email') }}</x-components::form.radio-option>
                    </x-components::form.radio>
                    !
                </x-components::form.group-top-3>
                <x-components::form.group-top>
                    <x-components::form.label for="roles[]">
                        {{ __('Roles') }}
                    </x-components::form.label>
                    <x-components::form.checkbox>
                        <x-components::grid cols="4">
                            <x-components::grid.block>
                                <x-components::form.checkbox-option id="client"
                                                                    name="roles[]"
                                                                    value="client"
                                                                    :checked="in_array(\App\Enum\Roles::CUSTOMER, $settings->roles)">{{ __('Customer') }}</x-components::form.checkbox-option>
                            </x-components::grid.block>
                            @if (current_team()->isRoot())
                                <x-components::grid.block>
                                    <x-components::form.checkbox-option id="physiologist"
                                                                        name="roles[]"
                                                                        value="physiologist"
                                                                        :checked="in_array(\App\Enum\Roles::PHYSIOLOGIST, $settings->roles)">{{ __('Physiologist') }}</x-components::form.checkbox-option>
                                </x-components::grid.block>
                            @endif
                            <x-components::grid.block>
                                <x-components::form.checkbox-option id="coach"
                                                                    name="roles[]"
                                                                    value="coach"
                                                                    :checked="in_array(\App\Enum\Roles::COACH, $settings->roles)">{{ __('Coach') }}</x-components::form.checkbox-option>
                            </x-components::grid.block>
                            <x-components::grid.block>
                                <x-components::form.checkbox-option id="admin"
                                                                    name="roles[]"
                                                                    value="admin"
                                                                    :checked="in_array(\App\Enum\Roles::ADMIN, $settings->roles)">{{ __('Admin') }}</x-components::form.checkbox-option>
                            </x-components::grid.block>
                        </x-components::grid>
                    </x-components::form.checkbox>
                </x-components::form.group-top>
            </x-components::form.fieldset-top>
            <x-components::form.button-group>
                <x-components::form.button action
                                           href="{{ route('teams.members') }}">{{ __('Cancel') }}</x-components::form.button>
                <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
