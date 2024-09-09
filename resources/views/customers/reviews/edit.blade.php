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

            <x-components::form action="{{ route('customers.reviews.update', [$customer, $review]) }}"
                                method="post">
                <x-slot name="customMethod">@method('PUT')</x-slot>
                <x-components::form.fieldset-top>
                    <x-components::form.group-top>
                        <x-components::form.label required
                                                  for="name">{{ __('Name') }}</x-components::form.label>
                        <x-components::form.input name="name" id="name" :value="old('name', $review->name)"/>
                    </x-components::form.group-top>
                    <x-components::form.group-top-3>
                        <x-components::form.label required
                                                  for="opens_at">{{ __('Opens at')  }}</x-components::form.label>
                        <x-components::form.date id="opens_at" name="opens_at"
                                                 :value="old('opens_at', $review->opens_at->format('Y-m-d'))"/>
                    </x-components::form.group-top-3>
                    <x-components::form.group-top-3>
                        <x-components::form.label required
                                                  for="closes_at">{{ __('Closes at')  }}</x-components::form.label>
                        <x-components::form.date id="closes_at" name="closes_at"
                                                 :value="old('closes_at', $review->closes_at->format('Y-m-d'))"/>
                    </x-components::form.group-top-3>
                    <x-components::form.group-top-3>
                        <x-components::form.label required
                                                  for="visible_at">{{ __('Results visible at')  }}</x-components::form.label>
                        <x-components::form.date id="visible_at" name="visible_at"
                                                 :value="old('visible_at', $review->visible_at->format('Y-m-d'))"/>
                    </x-components::form.group-top-3>
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
