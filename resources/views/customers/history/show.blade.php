<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            {{ __(':name', ['name' => $customer->name]) }}
            <x-slot name="suffix">
                {{ __('History for :date', ['date' => date_format_helper(\Carbon\Carbon::createFromFormat('Y-m-d', $date))->get_dmy()]) }}
            </x-slot>
            <x-slot name="actions">
                <x-button.big
                        icon="far fa-chevron-left"
                        href="javascript:history.go(-1);"
                        :outline-white="!current_team()->colorIsLight()"
                        :outline-black="current_team()->colorIsLight()">
                    Go back
                </x-button.big>
            </x-slot>
        </x-page-header>
    </x-slot>

    @include('customers.menu')
    <div class="p-6">
        @foreach($answers as $answer)
            @include('questions.answer')
        @endforeach
    </div>
</x-app-layout>
