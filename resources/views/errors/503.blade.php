@php
    $layout = config('pretty-error-pages.layout');
    if (array_key_exists('503', config('pretty-error-pages.layout-per-page'))) {
        $layout = config('pretty-error-pages.layout-per-page.503');
    }
@endphp

@switch($layout)
    @case('form')
    <x-pretty-error-pages::form-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.503.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.503.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.503.message') }}"
    />
    @break
    @case('simple')
    <x-pretty-error-pages::simple-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.503.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.503.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.503.message') }}"
    />
    @break
@endswitch
