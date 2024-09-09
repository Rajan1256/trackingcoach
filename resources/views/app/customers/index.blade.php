<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Pair app') }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('app.pair') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <x-slot name="extras">
        @foreach ($customers as $customer)
            <div class="p-16 mt-6 bg-white">
                <p class="flex justify-end font-bold text-xl">
                    <a href="{{ route('app.customers.show', [$customer]) }}" download>
                        <i class="fas fa-download"></i>
                    </a>
                </p>
                <p class="flex justify-center font-bold text-xl">{{ $customer->name }}</p>
                <p class="flex justify-center font-bold text-xl">{{ $customer->email }}</p>
                <p class="mb-12 flex justify-center font-bold text-xl">{{ __('To pair the app with your account scan the QR code below') }}</p>
                <div class="flex w-full justify-center">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($customer->paired_app_token) !!}
                </div>
            </div>
        @endforeach
    </x-slot>
</x-app-layout>
