<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
            @elseif(trim($slot) === 'Welcome')
                <img src="{{ asset('img/trackingcoach-welcome.png') }}" width="150" style="max-height: 100px; max-width: 150px;">
            @elseif(trim($slot) === 'Consent')
                <img src="{{ asset('img/trackingcoach.svg') }}" width="150" style="max-height: 100px; max-width: 150px;">
            @elseif(current_team())
                <img src="{{ current_team()->logo }}" width="150" style="max-height: 100px; max-width: 150px;">
            @else
                <img src="{{ asset('img/trackingcoach.svg') }}" width="150" style="max-height: 100px; max-width: 150px;">
            @endif
        </a>
    </td>
</tr>
