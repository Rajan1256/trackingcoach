<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Before you continue') }}
        </x-page-header>
    </x-slot>

    <x-slot name="extras">
        <x-components::form method="post" :action="route('consents.accept.store')">
            <x-components::form.fieldset>
                <div class="p-6 pl-10 mb-4 bg-white shadow relative">
                    <p class="text-md">{{ __('We want to be as clear and transparent as possible, about what we do with your data, what you can expect from us and what we expect from you. Please review each document and section below, and, if you agree, give your consent. We will send you a copy of the approved files.') }}</p>
                </div>
                @foreach($consents as $consent)
                    @if ($errors->has('consent['.$consent->id.']'))
                        @foreach($errors->get('consent['.$consent->id.']') as $error)
                            <x-components::alert.error>{{ $error }}</x-components::alert.error>
                        @endforeach
                    @endif
                    <div class="p-6 pl-10 mb-4 bg-white shadow relative">
                        @if ($consent->users->contains(\Illuminate\Support\Facades\Auth::user()))
                            <div class="rounded-full bg-green-200 h-12 w-12 -left-6 flex items-center justify-center absolute">
                                <i class="fas fa-check text-2xl"></i>
                            </div>
                        @else
                            <div class="rounded-full bg-gray-200 h-12 w-12 -left-6 flex items-center justify-center absolute">
                                <i class="fad fa-hourglass-start text-2xl"></i>
                            </div>
                        @endif

                        <div>
                            <h2 class="text-xl">{{ $consent->name }}</h2>
                            <p class="text-md my-3">{{ substr($consent->description, 0, 500) }}</p>

                            @if ($consent->hasMedia('pdf'))
                                <p class="my-3">
                                    <a href="{{ route('consents.download', [$consent, $consent->getFirstMedia('pdf')]) }}"
                                       class="text-blue-400 hover:text-blue-800" target="_blank">
                                        <i class="far fa-paperclip"></i>
                                        {{ ("Download attachment") }}
                                    </a>
                                </p>
                            @endif

                            @if ($consent->users->contains(\Illuminate\Support\Facades\Auth::user()))
                                <span class="italic">
                            {{ __('You confirmed reading this document on :DATE', ['DATE' => date_format_helper($consent->users->find(\Illuminate\Support\Facades\Auth::user())->pivot->created_at)->get_long_day_with_date_and_year()]) }}
                        </span>
                            @else
                                <x-components::form.group class="px-0">
                                    <x-components::form.checkbox>
                                        <x-components::form.checkbox-option name="consent[{{ $consent->id }}]"
                                                                            value="{{ $consent->id }}">
                                            {{ $consent->confirmation_text }}
                                        </x-components::form.checkbox-option>
                                    </x-components::form.checkbox>
                                </x-components::form.group>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="p-6 pl-10 mb-4 bg-white shadow relative">
                    <x-components::form.button submit primary
                                               class="ml-0">{{ __('Submit') }}</x-components::form.button>
                </div>
            </x-components::form.fieldset>
        </x-components::form>
    </x-slot>
</x-app-layout>
