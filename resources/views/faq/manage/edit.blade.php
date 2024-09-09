<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __('Manage FAQ') }}

            <x-slot name="actions">
                <x-button.big :outline-white="!current_team()->colorIsLight()"
                              :outline-black="current_team()->colorIsLight()"
                              icon="far fa-chevron-left"
                              href="{{ route('faq.manage.index') }}">
                    {{ __('Go back') }}</x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="px-6">
        <x-components::form method="post" action="{{ route('faq.manage.update', [$faq]) }}"
                            enctype="multipart/form-data">
            <x-slot name="customMethod">@method('PUT')</x-slot>
            <x-components::form.fieldset>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Question') }}</x-components::form.label>
                    <x-components::form.input name="question" value="{{ old('question', $faq->question) }}"/>
                </x-components::form.group-top>
                <x-components::form.group-top>
                    <x-components::form.label>{{ __('Answer') }}</x-components::form.label>
                    <x-components::form.trix name="answer"
                                             id="answer">{{ old('answer', $faq->answer) }}</x-components::form.trix>
                </x-components::form.group-top>
            </x-components::form.fieldset>
            <x-components::form.button-group>
                <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
            </x-components::form.button-group>
        </x-components::form>
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
