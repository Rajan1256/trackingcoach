@if ( $goal = \App\Models\Goal::find($question->model->options->get('goal')) )
    <p>
        <strong>{{ __("Goal") }}:</strong> {{ $goal->name }}
    </p>
@endif

<x-components::grid cols="7" class="items-end text-center">
    @foreach(range(-3,3) as $i)
        <x-components::grid.block>
            {{ __('trackingcoach.questions.options.' . \App\Questions\ZeroCenteredSevenScale::$scaleTypes[$question->model->options->get('scaleType', 'effectiveness')][$i + 3]) }}
            <br/>
            {{ $i }}
        </x-components::grid.block>
    @endforeach
</x-components::grid>
<x-components::form.group-top>
    <x-components::form.radio defaultStyle="" class="grid grid-cols-7 grid-flow-row"
                              name="answers[{{ $question->model->getLatestVersion()->id }}]">
        @foreach(range(-3,3) as $i)
            <x-components::form.radio-option name="answers[{{ $question->model->getLatestVersion()->id }}]"
                                             value="{{ $i }}"
                                             class="justify-center"/>
        @endforeach
    </x-components::form.radio>
</x-components::form.group-top>
