<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="text-center">
                <img src="{{ asset('img/trackingcoach.svg') }}" style="max-width: 340px; display: inline-block;" />
            </a>
        </x-slot>

        {{ __('You\'re already logged in, please select the team you want to navigate to:') }}
        <div class="mt-3">
            <ul>
                @foreach(Auth::user()->teams as $team)
                    <li class="mt-1 underline"><a href="https://{{ $team->fqdn }}">{{ $team->name }}</a></li>
                @endforeach
            </ul>
        </div>
    </x-auth-card>
</x-guest-layout>
<style>
    .gap-y-6 {
        row-gap: 0.5rem;
    }
</style>
