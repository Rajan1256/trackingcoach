<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Consents') }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('consents.index') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
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


    <x-slot name="extras">
        <section class="bg-white shadow">
            <div class="p-6">
                <h1 class="text-2xl">{{ __("Before you continue") }}</h1>
                <p class="mt-1 mb-3">
                    {{ __("We want to be as clear and transparent as possible, about what we do with your data, what you can expect from us and what we expect from you. Please review each document and section below, and, if you agree, give your consent. We will send you a copy of the approved files.") }}
                </p>

                <x-components::form method="post" enctype="multipart/form-data">
                    <x-components::form.fieldset>
                        <h2 class="text-xl mb-1">{{ $consent->name }}</h2>
                        <p class="!mt-0">{{ $consent->description }}</p>

                        @if ($consent->hasMedia('pdf'))
                            <p class="my-3">
                                <a href="{{ route('consents.download', [$consent, $consent->getFirstMedia('pdf')]) }}"
                                   class="text-blue-400 hover:text-blue-800" target="_blank">
                                    <i class="far fa-paperclip"></i>
                                    {{ ("Download attachment") }}
                                </a>
                            </p>
                        @endif

                        <x-components::form.group class="px-0">
                            <x-components::form.checkbox>
                                <x-components::form.checkbox-option>
                                    {{ $consent->confirmation_text }}
                                </x-components::form.checkbox-option>
                            </x-components::form.checkbox>
                        </x-components::form.group>
                    </x-components::form.fieldset>
                </x-components::form>
            </div>
        </section>
    </x-slot>

    <div class="p-6">
        <x-components::table>
            <x-components::table.header>
                <x-components::table.heading>{{ __('Name') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Accepted at') }}</x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                @foreach($users as $user)
                    <x-components::table.row>
                        <x-components::table.column>{{ $user->name }}</x-components::table.column>
                        <x-components::table.column>{{ $user->pivot->created_at->format('H:i d-m-Y') }}</x-components::table.column>
                    </x-components::table.row>
                @endforeach
            </x-components::table.body>
        </x-components::table>
        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
