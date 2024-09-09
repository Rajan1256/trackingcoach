<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="text-center">
                <img src="{{ asset('img/trackingcoach.svg') }}" style="max-width: 340px; display: inline-block;" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors"/>

        <x-components::form method="post" action="{{ route('register') }}" enctype='multipart/form-data'>
            <x-components::form.fieldset-top>
{{--                <div class="sm:col-span-6">--}}
{{--                    <h1 class="text-xl font-bold mb-4">Register now</h1>--}}
{{--                </div>--}}
                <div class="flex sm:col-span-6 gap-x-6">
                    <div class="w-100">
                        <x-components::form.group-top>
                            <x-components::form.label for="first_name" required>{{ __('First name') }}</x-components::form.label>
                            <x-components::form.input name="first_name" id="first_name" value="{{ old('first_name') }}" required/>
                        </x-components::form.group-top>
                    </div>
                    <div class="w-100">
                        <x-components::form.group-top>
                            <x-components::form.label for="last_name" required>{{ __('Last name') }}</x-components::form.label>
                            <x-components::form.input name="last_name" id="last_name" value="{{ old('last_name') }}" required/>
                        </x-components::form.group-top>
                    </div>
                </div>

                <x-components::form.group-top>
                    <x-components::form.label for="email" required>{{ __('Email') }}</x-components::form.label>
                    <x-components::form.input name="email" id="email" value="{{ old('email') }}" required/>
                </x-components::form.group-top>

                <div class="sm:col-span-6 gap-x-6 mt-4">
                    <div style="background: #f2f2f2; border: 1px solid #eee; border-radius: 8px; padding: 20px; color: #999; font-size: 14px;">
                        &#8226; The password must be at least 8 characters. <br/>
                        &#8226; The password must contain at least one lowercase letter, one uppercase letter, one digit and one special character (@$!%*#?&).
                    </div>
                </div>
            <x-components::form.group-top>
                    <x-components::form.label for="password" required>{{ __('Password') }}</x-components::form.label>
                    <x-components::form.input name="password" id="password" type="password" required/>
                </x-components::form.group-top>

                <x-components::form.group-top>
                    <x-components::form.label for="password_confirmation" required>{{ __('Confirm Password') }}</x-components::form.label>
                    <x-components::form.input name="password_confirmation" id="password_confirmation" type="password" required/>
                </x-components::form.group-top>

                <x-components::form.group-top>
                    <x-components::form.label for="company" required>{{ __('Company') }}</x-components::form.label>
                    <x-components::form.input name="company" id="company" value="{{ old('company') }}" required/>
                </x-components::form.group-top>

                <x-components::form.group-top>
                    <x-components::form.label
                            for="fqdn" required>{{ __('Your custom domain') }}</x-components::form.label>
                    <x-components::form.input prefix="https://" suffix=".{{ config('app.domain')  }}"
                                              name="fqdn"
                                              id="fqdn" value="{{ old('fqdn') }}"
                                              required/>
                </x-components::form.group-top>

                <div class="sm:col-span-6 gap-x-6 mt-4">
                    <div style="background: #f2f2f2; border: 1px solid #eee; border-radius: 8px; padding: 20px; color: #999; font-size: 11px;">
                    Note: ({{ __('Your Custom Domain Note') }})
                    </div>
                </div>

                <x-components::form.group-top>
                    <x-components::form.label
                            for="timezone" required>{{ __('Timezone') }}</x-components::form.label>
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
                            for="logo">{{ __('Logo') }}</x-components::form.label>
                    <x-components::form.file name="logo" id="logo"/>
                </x-components::form.group-top>
            </x-components::form.fieldset-top>

            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Register') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </x-auth-card>
</x-guest-layout>
<style>
    .gap-y-6 {
        row-gap: 0.5rem;
    }
</style>
