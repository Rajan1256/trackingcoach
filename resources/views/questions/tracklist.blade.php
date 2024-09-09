<x-question-layout>
    <x-components::headers.text>{{ __("Tracklist for :date", ['date' => date_format_helper(Carbon\Carbon::now())->get_dmy() ]) }}</x-components::headers.text>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-components::form method="post">
                        <x-components::form.fieldset>
                            @foreach($questions as $question)
                                <h3>
                                    <strong>{{ isset($customer) ? replaceNames($question->model->name, $customer) : $question->model->name }}</strong>
                                </h3>
                                @if ( $question->model->description )
                                    <p class="vraagUitleg">{!! nl2br(e(isset($client) ? replaceNames($question->model->description, $customer) : $question->model->description)) !!}</p>
                                @endif

                                @include('questions.' . $question->viewFolder(). '.view')
                            @endforeach

                        </x-components::form.fieldset>
                        <x-components::form.button-group>
                            <x-components::form.button primary submit>
                                {{ __('Save') }}
                            </x-components::form.button>
                        </x-components::form.button-group>
                    </x-components::form>
                </div>
            </div>
        </div>
    </div>
</x-question-layout>
