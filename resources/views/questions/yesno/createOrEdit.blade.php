<x-components::form.group-top>
    <x-components::form.label :small="false">
        {{ __("Positive value") }}
    </x-components::form.label>
    <x-slot name="description">
        <p class="text-xs py-4">
            {{ __("The positive value is what you want to hear.") }}<br/>
            {{ __("For example, if the question is: \"Did you go to the gym today?\", the positive value is \"Yes\".") }}
            <br/>
            {{ __("When the question is \"Did you get angry today?\", the positive value is \"No\".") }}<br/>
            <strong>{{ __("A little help") }}
                :</strong> {{ __("The positive value is where the target is bigger than the start value") }}
        </p>
    </x-slot>
    <x-components::form.radio>
        <x-components::form.radio-option
                value="1"
                name="options[positive]"
                :checked="old('options.positive', isset($question) ? intval($question->options->get('positive')) : '') === 1">{{ ucfirst(trans('trackingcoach.questions.options.yes')) }}</x-components::form.radio-option>
        <x-components::form.radio-option
                value="0"
                name="options[positive]"
                :checked="old('options.positive', isset($question) ? intval($question->options->get('positive')) : '') === 0">{{ ucfirst(trans('trackingcoach.questions.options.no')) }}</x-components::form.radio-option>
    </x-components::form.radio>
</x-components::form.group-top>

<x-components::form.group-top>
    <x-components::form.label :small="false">{{ __("Target value") }}</x-components::form.label>
    <x-components::form.select name="options[target]">
        @foreach(range(1,7) as $i)
            <x-components::form.select-option value="{{ $i }}"
                                              :selected="old('options.target', isset($question) ? $question->options->get('target') : '') == $i">{{ $i }}
                x
            </x-components::form.select-option>
        @endforeach
    </x-components::form.select>
</x-components::form.group-top>

<x-components::form.group-top>
    <x-components::form.label :small="false">{{ __("Start value") }}</x-components::form.label>
    <x-components::form.select name="options[start]">
        @foreach(range(0,7) as $i)
            <x-components::form.select-option value="{{ $i }}"
                                              :selected="old('options.start', isset($question) ? $question->options->get('start') : '') == $i">{{ $i }}
                x
            </x-components::form.select-option>
        @endforeach
    </x-components::form.select>
</x-components::form.group-top>

@include('questions._includes.excludeWeekends')
