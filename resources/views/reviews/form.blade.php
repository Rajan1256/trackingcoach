<x-app-layout>
    <x-slot name="hideNavigation"></x-slot>
    <x-slot name="header">
        <x-page-header>
            {{ $customer->company }} {{ __('Progress Review') }}<br/>
            <span class="text-xl">{{ $customer->name }}</span>
        </x-page-header>
    </x-slot>

    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-components::alert.error>{{ $error }}</x-components::alert.error>
        @endforeach
    @endif

    <x-components::form method="post" action="">
        <x-components::form.intro>
            <x-components::form.description>
                @if ( strlen(strip_tags(trim($supporter->personal_note))) < 5)
                    <p>{{ __('Dear Mr./Mrs. :last_name,', ['last_name' => $supporter->last_name]) }}</p>
                    <p>{{ __('Thank you in advance for your participation in the :company Progress Review for :name. This short question survey will take only 5-10 minutes of your time.', ['company' => $customer->company, 'name' => $customer->name]) }}</p>
                    <p>{{ __('Your valuable input and opinion is an important contribution for :first_name. Your answers to this survey will be completely confidential and will be combined with at least 6 other supports’ answers.', ['first_name' => $customer->first_name]) }}</p>
                    <p>{{ __('If you have any queries about the survey please contact me via phone or e-mail. On behalf of :first_name, thank you for your cooperation.', ['first_name' => $customer->first_name]) }}</p>
                    <p>{{ $coach->name }}<br/>
                        {{ $coach->email }}<br/>
                        {{ $customer->company }}</p>
                @else
                    <p>
                        {!! nl2br($supporter->personal_note) !!}
                    </p>
                @endif
            </x-components::form.description>
        </x-components::form.intro>
        <x-components::form.fieldset-top>
            @foreach ( $questions as $question )
                @include('questions._base.view')
            @endforeach
        </x-components::form.fieldset-top>
        <x-components::form.button-group>
            <x-components::form.button submit primary>{{ __('Save') }}</x-components::form.button>
        </x-components::form.button-group>
    </x-components::form>
</x-app-layout>
