<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Edit 360 interview') }}</x-slot>
            <x-slot name="actions">
                <x-button.big
                        icon="far fa-chevron-left"
                        :href="route('customers.interviews.index', $customer)"
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()">
                    Go back
                </x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">
            <x-components::form action="{{ route('customers.interviews.update', [$customer, $interview]) }}"
                                method="post">
                <x-slot name="customMethod">@method('put')</x-slot>
                <x-components::form.fieldset>
                    <x-components::form.group-top>
                        @if ($errors->has('date'))
                            <x-slot name="alert">
                                <x-components::alert.error>
                                    @foreach ($errors->get('date') as $error)
                                        {{ $error }} @if ($loop->index > 0) <br/> @endif
                                    @endforeach
                                </x-components::alert.error>
                            </x-slot>
                        @endif
                        <x-components::form.label for="date">{{ __('Date') }}</x-components::form.label>
                        <x-components::form.date id="date" name="date" class="max-w-lg"
                                                 value="{{ $interview->date->format('Y-m-d') }}"></x-components::form.date>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        @if ($errors->has('continue'))
                            <x-slot name="alert">
                                <x-components::alert.error>
                                    @foreach ($errors->get('continue') as $error)
                                        {{ $error }} @if ($loop->index > 0) <br/> @endif
                                    @endforeach
                                </x-components::alert.error>
                            </x-slot>
                        @endif
                        <x-components::form.label
                                class="text-2xl">{{ __('CONTINUE') }}</x-components::form.label>
                        <x-components::form.description
                                class="italic">{{ __('What should you CONTINUE doing because it makes you more effective? (QUALITIES)') }}</x-components::form.description>
                        <x-components::form.repeating name="continue"
                                                      :data="$interview->continue">
                            <x-components::form.textarea name="continue[]"
                                                         x-model="field.value"></x-components::form.textarea>
                        </x-components::form.repeating>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        @if ($errors->has('start'))
                            <x-slot name="alert">
                                <x-components::alert.error>
                                    @foreach ($errors->get('start') as $error)
                                        {{ $error }} @if ($loop->index > 0) <br/> @endif
                                    @endforeach
                                </x-components::alert.error>
                            </x-slot>
                        @endif
                        <x-components::form.label
                                class="text-2xl">{{ __('START') }}</x-components::form.label>
                        <x-components::form.description
                                class="italic">{{ __('What could you START doing or DOING MORE of to get more effective? (SUGGESTIONS)') }}</x-components::form.description>
                        <x-components::form.repeating name="start"
                                                      :data="$interview->start">
                            <x-components::form.textarea name="start[]"
                                                         x-model="field.value"></x-components::form.textarea>
                        </x-components::form.repeating>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        @if ($errors->has('stop'))
                            <x-slot name="alert">
                                <x-components::alert.error>
                                    @foreach ($errors->get('stop') as $error)
                                        {{ $error }} @if ($loop->index > 0) <br/> @endif
                                    @endforeach
                                </x-components::alert.error>
                            </x-slot>
                        @endif
                        <x-components::form.label
                                class="text-2xl">{{ __('STOP') }}</x-components::form.label>
                        <x-components::form.description
                                class="italic">{{ __('What should you STOP doing because it makes you in-effective?') }}</x-components::form.description>
                        <x-components::form.repeating name="stop"
                                                      :data="$interview->stop">
                            <x-components::form.textarea name="stop[]"
                                                         x-model="field.value"></x-components::form.textarea>
                        </x-components::form.repeating>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        @if ($errors->has('best'))
                            <x-slot name="alert">
                                <x-components::alert.error>
                                    @foreach ($errors->get('best') as $error)
                                        {{ $error }} @if ($loop->index > 0) <br/> @endif
                                    @endforeach
                                </x-components::alert.error>
                            </x-slot>
                        @endif
                        <x-components::form.label
                                class="text-2xl">{{ __('BEST!') }}</x-components::form.label>
                        <x-components::form.description
                                class="italic">{{ __('Describe the environment that brings out the BEST in this person.') }}</x-components::form.description>
                        <x-components::form.repeating name="best"
                                                      :data="$interview->best">
                            <x-components::form.textarea name="best[]"
                                                         x-model="field.value"></x-components::form.textarea>
                        </x-components::form.repeating>
                    </x-components::form.group-top>
                    <x-components::form.group-top>
                        @if ($errors->has('worst'))
                            <x-slot name="alert">
                                <x-components::alert.error>
                                    @foreach ($errors->get('worst') as $error)
                                        {{ $error }} @if ($loop->index > 0) <br/> @endif
                                    @endforeach
                                </x-components::alert.error>
                            </x-slot>
                        @endif
                        <x-components::form.label
                                class="text-2xl">{{ __('WORST!') }}</x-components::form.label>
                        <x-components::form.description
                                class="italic">{{ __('Describe the environment that brings out the WORST in this person.') }}</x-components::form.description>
                        <x-components::form.repeating name="worst"
                                                      :data="$interview->worst">
                            <x-components::form.textarea name="worst[]"
                                                         x-model="field.value"></x-components::form.textarea>
                        </x-components::form.repeating>
                    </x-components::form.group-top>
                </x-components::form.fieldset>
                <x-components::form.button-group>
                    <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
                </x-components::form.button-group>
            </x-components::form>
        </div>
    </div>
</x-app-layout>
