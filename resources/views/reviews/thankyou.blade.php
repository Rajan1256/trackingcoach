<x-app-layout>
    <x-slot name="hideNavigation"></x-slot>
    <x-slot name="header">
        <x-page-header>
            {{ $customer->company }} {{ __('Progress Review') }}<br/>
            <span class="text-xl">{{ $customer->name }}</span>
        </x-page-header>
    </x-slot>

    <div class="w-full flex flex-col justify-center text-center p-20">
        <i class="fal fa-thumbs-up text-9xl mb-14"></i>
        <span class="text-4xl">{{ __('Thank you!') }}</span>
    </div>
</x-app-layout>
