@props([
    'selected' => false,
])

<option {{ $attributes }} @if($selected) selected @endif >{{ $slot }}</option>
