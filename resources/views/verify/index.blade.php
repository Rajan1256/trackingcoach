<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="text-center">
                <img src="{{ asset('img/trackingcoach.svg') }}" style="max-width: 340px; display: inline-block;" />
            </a>
        </x-slot>
        @if(session()->has('resend'))
            <div class="text-green-600">
                {{ session()->get('resend') }}
            </div>
        @endif

        <div class="mt-4">
            {{ __('Please verify your email address (:masked_email) before continuing.', ['masked_email' => Auth::user()->mask_email]) }}
            {{ __('Make sure to check your spam inbox.') }}
        </div>
        <div class="mt-4 flex justify-between items-center">
            <small><a href="{{ route('verify.resend') }}">{{ __('Didn\'t receive an email? Click here.') }}</a></small>

            <form method="post" action="{{ route('logout') }}">
                @csrf
                <small><button type="submit">Logout</button></small>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
<style>
    .gap-y-6 {
        row-gap: 0.5rem;
    }
</style>
