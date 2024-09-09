<x-components::form.group-top>
    <x-components::form.label :small="false">{{ __("Scale type") }}</x-components::form.label>
    <x-components::form.select name="options[scaleType]">
        @foreach(\App\Questions\ZeroCenteredSevenScale::$scaleTypes as $name => $values)
            <x-components::form.select-option value="{{ $name }}"
                                              :selected="old('options.scaleType', isset($question) ? $question->options->get('scaleType', 'effectiveness') : '') == $name">
                @foreach ($values as $value)
                    {{ trans('trackingcoach.questions.options.' . $value) }}@if($loop->index != $loop->count - 1),@endif
                @endforeach
            </x-components::form.select-option>
        @endforeach
    </x-components::form.select>
</x-components::form.group-top>

@if (isset($customer))
    <x-components::form.group-top>
        <x-components::form.label :small="false">{{ __("Related goal") }}</x-components::form.label>
        <x-components::form.select name="options[goal]">
            <x-components::form.select-option value=""
                                              :selected="old('options.goal', isset($question) ? $question->options->get('goal') : '') == ''">
                {{ __('No goal') }}
            </x-components::form.select-option>
            @foreach($customer->goals as $goal)
                <x-components::form.select-option value="{{ $goal->id }}"
                                                  :selected="old('options.goal', isset($question) ? $question->options->get('goal') : '') == $goal->id">
                    {{ $goal->getTranslation('name', Auth::user()->locale) }}
                </x-components::form.select-option>
            @endforeach
        </x-components::form.select>
    </x-components::form.group-top>
@endif
