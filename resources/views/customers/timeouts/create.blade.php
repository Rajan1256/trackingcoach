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

    @include('customers.menu')
    <div class="px-6">
        <x-components::form method="post" action="{{ route('customers.timeouts.store', [$customer]) }}">
            <x-components::form.fieldset>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Start') }}</x-components::form.label>
                    <x-components::form.date name="start"></x-components::form.date>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('End') }}</x-components::form.label>
                    <x-components::form.date name="end"></x-components::form.date>
                </x-components::form.group-top>
            </x-components::form.fieldset>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Create') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
