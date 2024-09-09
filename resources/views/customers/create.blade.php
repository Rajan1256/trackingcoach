<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Create a new customer') }}
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
        <div class="p-6 bg-white border-b border-gray-200" id="customer-create">
            <x-components::form method="post" :action="route('customers.store')">
                <x-components::form.fieldset-top>
                    <h2 class="text-2xl sm:col-span-6">{{ __('Contract details') }}</h2>
                    <x-components::form.group-top-2>
                        <x-components::form.label required>{{ __('First name') }}</x-components::form.label>
                        <x-components::form.input name="first_name" value="{{ old('first_name') }}"/>
                    </x-components::form.group-top-2>
                    <x-components::form.group-top-2>
                        <x-components::form.label required>{{ __('Last name') }}</x-components::form.label>
                        <x-components::form.input name="last_name" value="{{ old('last_name') }}"/>
                    </x-components::form.group-top-2>
                    <x-components::form.group-top>
                        <x-components::form.label required>{{ __('Email') }}</x-components::form.label>
                        <x-components::form.input name="email" value="{{ old('email') }}"/>
                    </x-components::form.group-top>
                    @if(current_team()->isRoot())
                        <x-components::form.group-top-2>
                            <x-components::form.label optional>{{ __('Coach') }}</x-components::form.label>
                            <x-components::form.select name="coach" id="coach"
                                                       :disabled="Auth::user()->hasCurrentTeamRole([App\Enum\Roles::COACH]) && !Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN])">
                                <x-components::form.select-option
                                        :selected="old('coach') == null"
                                        value="null">
                                    {{ __('Select a coach') }}
                                </x-components::form.select-option>
                                @foreach ($coaches as $coach)
                                    <x-components::form.select-option
                                            :selected="old('coach', (Auth::user()->hasCurrentTeamRole([App\Enum\Roles::COACH]) && !Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN])) ? Auth::user()->id : '') == $coach->id"
                                            value="{{ $coach->id }}">{{ $coach->name }}
                                        - {{ $coach->email }}</x-components::form.select-option>
                                @endforeach
                            </x-components::form.select>
                        </x-components::form.group-top-2>
                        <x-components::form.group-top-2>
                            <x-components::form.label>{{ __('Physiologists') }}</x-components::form.label>
                            <x-components::form.select name="physiologist">
                                <x-components::form.select-option
                                        :selected="old('physiologist') == null"
                                        value="null">
                                    {{ __('Select a physiologist') }}
                                </x-components::form.select-option>
                                @foreach ($physiologists as $physiologist)
                                    <x-components::form.select-option
                                            :selected="old('physiologist') == $physiologist->id"
                                            value="{{ $physiologist->id }}">{{ $physiologist->name }}
                                        - {{ $physiologist->email }}</x-components::form.select-option>
                                @endforeach
                            </x-components::form.select>
                        </x-components::form.group-top-2>
                    @else
                        <x-components::form.group-top>
                            <x-components::form.label optional>{{ __('Coach') }}</x-components::form.label>
                            <x-components::form.select name="coach" id="coach"
                                                       :disabled="Auth::user()->hasCurrentTeamRole([App\Enum\Roles::COACH]) && !Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN])">
                                <x-components::form.select-option
                                        :selected="old('coach') == null"
                                        value="null">
                                    {{ __('Select a coach') }}
                                </x-components::form.select-option>
                                @foreach ($coaches as $coach)
                                    <x-components::form.select-option
                                            :selected="old('coach', (Auth::user()->hasCurrentTeamRole([App\Enum\Roles::COACH]) && !Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN])) ? Auth::user()->id : '') == $coach->id"
                                            value="{{ $coach->id }}">{{ $coach->name }}
                                        - {{ $coach->email }}</x-components::form.select-option>
                                @endforeach
                            </x-components::form.select>
                        </x-components::form.group-top>
                    @endif
                    <x-components::form.group-top>
                        <x-components::form.label id="days">{{ __('5 or 7 days per week') }}</x-components::form.label>
                        <x-components::form.radio defaultStyle="" class="grid grid-cols-12 grid-flow-row">
                            <x-components::form.radio-option value="5" name="days_per_week"
                                                             :checked="old('days_per_week') === 5">
                                5
                            </x-components::form.radio-option>
                            <x-components::form.radio-option value="7" name="days_per_week"
                                                             :checked="old('days_per_week') === 7">
                                7
                            </x-components::form.radio-option>
                        </x-components::form.radio>
                    </x-components::form.group-top>
                    <h2 class="text-2xl sm:col-span-6">{{ __('Automatic invites') }}</h2>
                    <x-components::form.group-top>
                        <x-components::form.label
                                required>{{ __('Auto invite time (in timezone of user)') }}</x-components::form.label>
                        <x-components::form.input name="filled_auto_invite_time" id="auto-invite"
                                                  :value="old('filled_auto_invite_time')"/>
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
