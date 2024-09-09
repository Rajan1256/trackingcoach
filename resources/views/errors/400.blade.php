@php
    $layout = config('pretty-error-pages.layout');
    if (array_key_exists('400', config('pretty-error-pages.layout-per-page'))) {
        $layout = config('pretty-error-pages.layout-per-page.400');
    }
@endphp

@switch($layout)
    @case('form')
    <x-pretty-error-pages::form-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.400.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.400.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.400.message') }}"
    />
    @break
    @case('simple')
    <x-pretty-error-pages::simple-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.400.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.400.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.400.message') }}"
    />
    @break
@endswitch
