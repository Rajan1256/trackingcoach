{{--@formatter:off--}}
@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => url('/')])
New team registered
@endcomponent
@endslot

{{ __('Hi,') }}

{{ __('The following team has registered:') }}

@component('mail::panel')
    <strong>{{ __('First name') }}:</strong> {{ $user->first_name }}<br />
    <strong>{{ __('Last name') }}:</strong> {{ $user->last_name }}<br />
    <strong>{{ __('Email') }}:</strong> {{ $user->email }}<br />
    <strong>{{ __('Company') }}:</strong> {{ $team->company }}<br />
    <strong>{{ __('Domain') }}:</strong> {{ $team->fqdn }}<br />
    <strong>{{ __('Timezone') }}:</strong> {{ $team->timezone }}<br />
@endcomponent

{{ __('Greetings') }},

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
@endslot
@endcomponent

