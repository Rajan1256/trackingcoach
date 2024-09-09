@component('mail::message')
A new error form has been submitted.

@component('mail::panel')
    <strong>Time:</strong> {{ $date->format('H:i:s d-m-Y') }}<br/>
    <strong>Error code:</strong> {{ $code  }}<br/>
    <strong>First name:</strong> {{ $first_name }}<br/>
    <strong>Last name:</strong> {{ $last_name }}<br/>
    <strong>Email:</strong> {{ $email }}<br/>
    <strong>Description:</strong> {{ $description }}
@endcomponent
@endcomponent
