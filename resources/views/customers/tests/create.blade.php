<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">
                {{ __('New test') }}
            </x-slot>

            <x-slot name="actions">
                <x-button.big
                        icon="far fa-chevron-left"
                        :href="route('customers.tests.index', $customer)"
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()">
                    Go back
                </x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="px-6 bg-white border-b border-gray-200">

            <x-components::form enctype="multipart/form-data" method="post"
                                action="{{ route('customers.tests.store', [$customer]) }}">
                @include($test->getViewPath() . '.form')

                <x-components::form.hidden-group>
                    <x-components::form.hidden name="type" value="{{ get_class($test) }}"/>
                </x-components::form.hidden-group>
                <x-components::form.button-group>
                    <x-components::form.button primary submit>{{ __('Save') }}</x-components::form.button>
                </x-components::form.button-group>
            </x-components::form>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/trix.css') }}"></link>
        <script src="{{ asset('/js/trix.js') }}"></script>
    @endpush

    @push('scripts')
        <script>
            document.querySelectorAll('trix-editor').forEach((element) => {
                element.style.minHeight = '200px';
            })
        </script>
    @endpush
</x-app-layout>
