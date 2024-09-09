<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">{{ __('Daily questions') }}</x-slot>
        </x-page-header>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">
            You're logged in!
        </div>
    </div>
</x-app-layout>
