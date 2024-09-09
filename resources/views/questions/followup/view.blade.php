@if ( $goal = \App\Models\Goal::find($question->model->options->get('goal')) )
    <p>
        <strong>{{ __("Goal") }}:</strong> {{ $goal->name }}
    </p>
@endif

@php($scales = \App\Questions\FollowUp::$scaleTypes[$question->model->options->get('scaleType', 'followup')])
<x-components::grid :cols="count($scales)" class="items-end text-center">
    @foreach(range(0,count($scales)-1) as $i)
        <x-components::grid.block>
            {{ __('trackingcoach.questions.options.' . \App\Questions\FollowUp::$scaleTypes[$question->model->options->get('scaleType', 'followup')][$i]) }}
            <br/>
        </x-components::grid.block>
    @endforeach
</x-components::grid>
<x-components::form.group-top>
    <x-components::form.radio defaultStyle="" class="grid grid-cols-{{ count($scales) }} grid-flow-row"
                              name="answers[{{ $question->model->getLatestVersion()->id }}]">
        @foreach(range(0,count($scales)-1) as $i)
            <x-components::form.radio-option name="answers[{{ $question->model->getLatestVersion()->id }}]"
                                             value="{{ $i }}"
                                             class="justify-center"/>
        @endforeach
    </x-components::form.radio>
</x-components::form.group-top>
