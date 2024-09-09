<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @foreach(config('pretty-error-pages.css') as $css)
        <link href="{{ asset($css) }}" rel="stylesheet">
    @endforeach
    @foreach(config('pretty-error-pages.js') as $js)
        <script src="{{ asset($js) }}" type="text/javascript"></script>
    @endforeach
</head>
<body>
{{ $slot }}
</body>
</html>
