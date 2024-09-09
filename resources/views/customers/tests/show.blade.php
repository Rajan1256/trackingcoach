<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}

            <x-slot name="suffix">
                {{ $test->data->getName() }}
            </x-slot>
            <x-slot name="actions">
                <x-button.big
                        icon="far fa-chevron-left"
                        :href="route('customers.tests.index', $customer)"
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()">
                    Go back
                </x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">
            @include($test->data->getViewPath() . '.show')
        </div>
    </div>
</x-app-layout>
