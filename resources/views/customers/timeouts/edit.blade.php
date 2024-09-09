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
        <x-components::form method="post"
                            action="{{ route('customers.timeouts.update', [$customer, $timeout]) }}">
            <x-slot name="customMethod">
                @method('PUT')
            </x-slot>
            <x-components::form.fieldset>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Start') }}</x-components::form.label>
                    <x-components::form.date name="start"
                                             value="{{ $timeout->start->format('Y-m-d') }}"></x-components::form.date>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('End') }}</x-components::form.label>
                    <x-components::form.date name="end"
                                             value="{{ $timeout->end->format('Y-m-d') }}"></x-components::form.date>
                </x-components::form.group-top>
            </x-components::form.fieldset>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Update') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
    </div>
</x-app-layout>
