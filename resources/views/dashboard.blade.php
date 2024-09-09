<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Dashboard') }}
        </x-page-header>
    </x-slot>

    <x-slot name="sidebar">
        @include('includes.sidebar.welcome-back')
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            You're logged in!
        </div>
    </div>
</x-app-layout>
