@if ( $goal = \App\Models\Goal::find($question->model->options->get('goal')) )
    <p>
        <strong>{{ __("Goal") }}:</strong> {{ $goal->name }}
    </p>
@endif

<x-components::form.group-top>
    <x-components::form.textarea
            name="answers[{{ $question->model->getLatestVersion()->id }}]"></x-components::form.textarea>
</x-components::form.group-top>
