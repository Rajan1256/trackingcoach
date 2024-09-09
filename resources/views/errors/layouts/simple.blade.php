@props([
    'error' => '',
    'title' => '',
    'message' => '',
])

<x-pretty-error-pages::main-error-page>
    <div class="min-h-screen pt-16 pb-12 flex flex-col bg-white">
        <main class="flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            @if (!empty(config('pretty-error-pages.logo')))
                <div class="flex-shrink-0 flex justify-center">
                    <a href="{{ config('pretty-error-pages.home') }}" class="inline-flex">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <img class="h-12 w-auto" src="{{ config('pretty-error-pages.logo') }}"
                             alt="{{ config('app.name') }}">
                    </a>
                </div>
            @endif
            <div class="py-16">
                <div class="text-center">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">{!! $error !!}</p>
                    <h1 class="mt-2 text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">{!! $title !!}</h1>
                    <p class="mt-2 text-base text-gray-500">{!! $message !!}</p>
                    <div class="mt-6">
                        <a href="{{ config('pretty-error-pages.home') }}"
                           class="text-base font-medium text-indigo-600 hover:text-indigo-500">{{ __('pretty-error-pages::pretty-error-pages.back-home') }}
                            <span aria-hidden="true"> &rarr;</span></a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-pretty-error-pages::main-error-page>
