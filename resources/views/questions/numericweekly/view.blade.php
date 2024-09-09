@if ( $goal = \App\Models\Goal::find($question->model->options->get('goal')) )
    <p>
        <strong>{{ __("Goal") }}:</strong> {{ $goal->name }}
    </p>
@endif

<x-components::form.group-top>
    <x-components::form.input step="0.1" type="number"  name="answers[{{ $question->model->getLatestVersion()->id }}]"></x-components::form.input>
</x-components::form.group-top>
