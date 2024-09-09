<x-app-layout>
    <x-slot name="hideNavigation">
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}">
                                <x-application-logo class="block h-10 w-auto fill-current text-gray-600"/>
                            </a>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="pt-7 pb-1 border-t border-gray-200">
                            <a href="{{ route('login') }}" class="font-medium text-base text-gray-800">{{ __('Login') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

    </x-slot>

    <x-slot name="extras">
        <div class="text-2xl mt-24 bg-white px-5 py-12 text-center shadow">
            {{ __('Sorry, this invite has expired.') }}
        </div>
    </x-slot>
</x-app-layout>
