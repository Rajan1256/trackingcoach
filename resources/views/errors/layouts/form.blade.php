@props([
    'error' => '',
    'title' => '',
    'message' => '',
])

<x-pretty-error-pages::main-error-page>
    <div class="bg-white">
        <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            @if (!empty(config('pretty-error-pages.logo')))
                <div class="flex-shrink-0 pt-16">
                    <img class="mx-auto h-12 w-auto"
                         src="{{ config('pretty-error-pages.logo') }}" alt="{{ config('app.name') }}">
                </div>
            @endif
            <div class="max-w-xl mx-auto py-16 sm:py-24">
                <div class="text-center">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">{!! $error !!}</p>
                    <h1 class="mt-2 text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">{!! $title !!}</h1>
                    <p class="mt-2 text-lg text-gray-500">{!! $message !!}</p>
                </div>
                <div class="mt-12">
                    <h2 class="mb-6 text-sm font-semibold text-gray-500 tracking-wide uppercase">
                        {{ __('pretty-error-pages::pretty-error-pages.let-us-know') }}
                    </h2>

                    @if(isset($errors) && $errors->any())
                        @foreach($errors->all() as $e)
                            <span class="text-red-600">{{ $e }}</span>
                        @endforeach
                    @endif

                    <form action="{{ route('pretty-error-pages.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" value="{{ $error }}" name="code"/>
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="first_name"
                                       class="block text-sm font-medium text-gray-700">{{ __('pretty-error-pages::pretty-error-pages.first_name') }}</label>
                                <input type="text" name="first_name" id="first_name" autocomplete="given-name"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="last_name"
                                       class="block text-sm font-medium text-gray-700">{{ __('pretty-error-pages::pretty-error-pages.last_name') }}</label>
                                <input type="text" name="last_name" id="last_name" autocomplete="family-name"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-6">
                                <label for="email-address"
                                       class="block text-sm font-medium text-gray-700">{{ __('pretty-error-pages::pretty-error-pages.email') }}</label>
                                <input type="text" name="email" id="email" autocomplete="email"
                                       class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-6">
                                <label for="description"
                                       class="block text-sm font-medium text-gray-700">{{ __('pretty-error-pages::pretty-error-pages.description') }}
                                    <span class="text-red-600">*</span></label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="3"
                                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('pretty-error-pages::pretty-error-pages.send') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-8">
                        <a href="{{ config('pretty-error-pages.home') }}"
                           class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                            {{ __('pretty-error-pages::pretty-error-pages.or-back-home') }}
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-pretty-error-pages::main-error-page>
