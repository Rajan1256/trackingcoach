<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="px-6 bg-white border-b border-gray-200">

            <x-components::form method="post"
                                action="{{ route('customers.supporters.update', [$customer, $supporter]) }}">
                <x-slot name="customMethod">@method('PUT')</x-slot>
                <x-components::form.fieldset-top>
                    <x-components::form.group-top-2>
                        <x-components::form.label required
                                                  for="first_name">{{ __('First name') }}</x-components::form.label>
                        <x-components::form.input id="first_name" name="first_name"
                                                  value="{{ old('first_name', $supporter->first_name) }}"/>
                    </x-components::form.group-top-2>
                    <x-components::form.group-top-2>
                        <x-components::form.label required
                                                  for="last_name">{{ __('Last name') }}</x-components::form.label>
                        <x-components::form.input id="last_name" name="last_name"
                                                  value="{{ old('last_name', $supporter->last_name) }}"/>
                    </x-components::form.group-top-2>
                    <x-components::form.group-top-2>
                        <x-components::form.label
                                for="email">{{ __('Email') }}</x-components::form.label>
                        <x-components::form.input id="email" name="email"
                                                  value="{{ old('email', $supporter->email) }}"/>
                    </x-components::form.group-top-2>
                    <x-components::form.group-top-2>
                        <x-components::form.label
                                for="phone">{{ __('Phone') }}</x-components::form.label>
                        <x-components::form.input id="phone" name="phone"
                                                  value="{{ old('phone', $supporter->phone) }}"/>
                    </x-components::form.group-top-2>
                    <x-components::form.group-top>
                        <x-components::form.label required
                                                  for="relationship">{{ __('Relationship') }}</x-components::form.label>
                        <x-components::form.select id="relationship" name="relationship">
                            <x-components::form.select-option value="self"
                                                              :selected="old('relationship', $supporter->relationship) === 'self'">{{ __('Self') }}</x-components::form.select-option>
                            <x-components::form.select-option value="boss"
                                                              :selected="old('relationship', $supporter->relationship) === 'boss'">{{ __('Boss(es)') }}</x-components::form.select-option>
                            <x-components::form.select-option value="directReport"
                                                              :selected="old('relationship', $supporter->relationship) === 'directReport'">{{ __('Direct Reports') }}</x-components::form.select-option>
                            <x-components::form.select-option value="peer"
                                                              :selected="old('relationship', $supporter->relationship) === 'peer'">{{ __('Peers') }}</x-components::form.select-option>
                            <x-components::form.select-option value="other"
                                                              :selected="old('relationship', $supporter->relationship) === 'other'">{{ __('Others') }}</x-components::form.select-option>
                        </x-components::form.select>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        <x-components::form.label
                                for="personal_note">{{ __('Personal note') }}</x-components::form.label>
                        <x-components::form.description
                                class="text-xs">{{ __('Leave a personal note for this stakeholder. This will be shown at the beginning of the questionnaire.') }}</x-components::form.description>
                        <x-components::form.textarea id="personal_note"
                                                     name="personal_note">{{ old('personal_note', $supporter->personal_note) }}</x-components::form.textarea>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        <x-components::form.label required
                                                  for="locale">{{ __('Locale') }}</x-components::form.label>
                        <x-components::form.select id="locale" name="locale">
                            @foreach(config('trackingcoach.languages') as $short => $full)
                                <x-components::form.select-option value="{{ $short }}"
                                                                  :selected="old('locale', $supporter->locale) === $short">{{ __($full)  }}</x-components::form.select-option>
                            @endforeach
                        </x-components::form.select>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        <x-components::form.label required
                                                  for="notification_method">{{ __('Notification method') }}</x-components::form.label>
                        <x-components::form.radio class="grid grid-cols-3 grid-flow-row space-y-0">
                            <x-components::form.radio-option name="notification_method" value="mail"
                                                             :checked="old('notification_method', $supporter->notification_method) === 'mail'">{{ __('Mail') }}</x-components::form.radio-option>
                            <x-components::form.radio-option name="notification_method" value="sms"
                                                             :checked="old('notification_method', $supporter->notification_method) === 'sms'">{{ __('SMS') }}</x-components::form.radio-option>
                            <x-components::form.radio-option name="notification_method" value="both"
                                                             :checked="old('notification_method', $supporter->notification_method) === 'both'">{{ __('Both') }}</x-components::form.radio-option>
                        </x-components::form.radio>
                    </x-components::form.group-top>
                </x-components::form.fieldset-top>
                <x-components::form.button-group>
                    <x-components::form.button primary submit>
                        {{ __('Save') }}
                    </x-components::form.button>
                </x-components::form.button-group>
            </x-components::form>
        </div>
    </div>
</x-app-layout>
