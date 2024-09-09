@php
    $layout = config('pretty-error-pages.layout');
    if (array_key_exists('402', config('pretty-error-pages.layout-per-page'))) {
        $layout = config('pretty-error-pages.layout-per-page.402');
    }
@endphp

@switch($layout)
    @case('form')
    <x-pretty-error-pages::form-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.402.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.402.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.402.message') }}"
    />
    @break
    @case('simple')
    <x-pretty-error-pages::simple-error-page
            title="{{ __('pretty-error-pages::pretty-error-pages.402.title') }}"
            error="{{ __('pretty-error-pages::pretty-error-pages.402.error') }}"
            message="{{ __('pretty-error-pages::pretty-error-pages.402.message') }}"
    />
    @break
@endswitch
