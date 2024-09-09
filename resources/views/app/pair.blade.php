<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Pair app') }}

            <x-slot name="actions">
                @if(current_team()->isRoot() && Auth::user()->hasCurrentTeamRole([App\Enum\Roles::ADMIN]))
                    <x-button.big
                            :outline-white="!current_team()->colorIsLight()"
                            :outline-black="current_team()->colorIsLight()"
                            href="{{ route('app.customers.index') }}">{{ __('Customer QR codes') }}</x-button.big>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="p-16">
        <p class="mb-12 flex justify-center font-bold text-xl">{{ __('To pair the app with your account scan the QR code below') }}</p>
        <div class="flex w-full justify-center">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate($token) !!}
        </div>
    </div>
</x-app-layout>
