<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
        </x-page-header>
    </x-slot>


    @if(session()->has('message'))
        <x-components::alert.success class="mb-5">{{ session()->get('message') }}</x-components::alert.success>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('customers.menu')
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="p-3 text-center mb-5">
                <h1 class="text-3xl font-bold">{{ __("Answers of :SUPPORTER_NAME:", ['SUPPORTER_NAME' => $supporter->name]) }}</h1>
            </div>

            @foreach($answers as $answer)
                @include('questions.answer')
            @endforeach
        </div>
    </div>
</x-app-layout>
