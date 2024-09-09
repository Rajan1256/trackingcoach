{{--@formatter:off--}}
@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => url('/')])
You got invited to a team
@endcomponent
@endslot

{{ __('Hi :first_name,', ['first_name' => $firstName]) }}

{{ __('You got invited to the :team team. Make sure to click the button below to accept the invite.', ['team' => $team->name]) }}

@component('mail::button', ['url' => route('accept.invite.show', $token)])
{{ __('Accept the invite') }}
@endcomponent

{{ __('Greetings') }},
{{ $team->name }}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
@endslot
@endcomponent

