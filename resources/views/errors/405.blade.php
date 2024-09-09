@php
    $layout = config('pretty-error-pages.layout');
    if (array_key_exists('405', config('pretty-error-pages.layout-per-page'))) {
        $layout = config('pretty-error-pages.layout-per-page.405');
    }
@endphp

@switch($layout)
    @case('form')
    <x-pretty-error-pages::form-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.405.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.405.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.405.message') }}"
    />
    @break
    @case('simple')
    <x-pretty-error-pages::simple-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.405.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.405.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.405.message') }}"
    />
    @break
@endswitch
