@if ( $goal = \App\Models\Goal::find($question->model->options->get('goal')) )
    <p>
        <strong>{{ __("Goal") }}:</strong> {{ $goal->name }}
    </p>
@endif

<x-components::grid cols="7" class="items-end text-center">
    @foreach(range(1, 7) as $i)
        <x-components::grid.block>
            @if ( $i == 1 || $i == 4 || $i == 7)
                {{ __('trackingcoach.questions.options.' . \App\Questions\SevenScale::$scaleTypes[$question->model->options->get('scaleType', 'greatness')][floor($i / 3)]) }}
                <br/>
            @endif
            {{ $i }}
        </x-components::grid.block>
    @endforeach
</x-components::grid>
<x-components::form.group-top>
    <x-components::form.radio defaultStyle="" class="grid grid-cols-7 grid-flow-row"
                              name="answers[{{ $question->model->getLatestVersion()->id }}]">
        @foreach(range(1, 7) as $i)
            <x-components::form.radio-option name="answers[{{ $question->model->getLatestVersion()->id }}]"
                                             value="{{ $i }}"
                                             class="justify-center"></x-components::form.radio-option>
        @endforeach
    </x-components::form.radio>
</x-components::form.group-top>
