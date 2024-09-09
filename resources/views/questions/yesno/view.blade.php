@if ( $goal = \App\Models\Goal::find($question->model->options->get('goal')) )
    <p>
        <strong>{{ __("Goal") }}:</strong> {{ $goal->name }}
    </p>
@endif

<x-components::form.group-top>
    <x-components::form.radio
            name="answers[{{ $question->model->getLatestVersion()->id }}]">
        <x-components::form.radio-option
            name="answers[{{ $question->model->getLatestVersion()->id }}]" value="1">{{ __('Yes') }}</x-components::form.radio-option>
        <x-components::form.radio-option
            name="answers[{{ $question->model->getLatestVersion()->id }}]" value="0">{{ __('No') }}</x-components::form.radio-option>
    </x-components::form.radio>
</x-components::form.group-top>
