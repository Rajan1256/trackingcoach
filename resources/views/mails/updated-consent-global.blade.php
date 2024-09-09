@component('mail::message')
{{ __("Dear :name,", ['name' => $user->first_name]) }}

{{ __("According to our records you recently gave us your consent to process your data.") }}

{!! __("For your and our records, this email acts as evidence for both parties to what you said <i>yes</i> to.") !!}
{{ __("Also, you can find the accompanying documents attached to this mail.") }}

{{ __("You agreed to the following:") }}

<ul>
@foreach ($consents as $consent)
<li>{{ $consent->name }}</li>
@endforeach
</ul>

{{ __("If you have any questions regarding your privacy, please contact us via anne-johan@topmind.com.") }}

{{ __("Thanks,") }}<br>
Trackingcoach
@endcomponent
