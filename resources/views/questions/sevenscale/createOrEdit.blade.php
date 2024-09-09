<x-components::form.group-top>
    <x-components::form.label :small="false">{{ __("Scale type") }}</x-components::form.label>
    <x-components::form.select name="options[scaleType]">
        @foreach(\App\Questions\SevenScale::$scaleTypes as $name => $values)
            <x-components::form.select-option value="{{ $name }}"
                                              :selected="old('options.scaleType', isset($question) ? $question->options->get('scaleType', 'greatness') : '') == $name">
                @foreach ($values as $value)
                    {{ trans('trackingcoach.questions.options.' . $value) }}@if($loop->index != $loop->count - 1),@endif
                @endforeach
            </x-components::form.select-option>
        @endforeach
    </x-components::form.select>
</x-components::form.group-top>

<x-components::table>
    <x-components::table.header>
        <x-components::table.heading></x-components::table.heading>
        @foreach (range(1,7) as $i)
            <x-components::table.heading>{{ $i }}</x-components::table.heading>
        @endforeach
    </x-components::table.header>
    <x-components::table.body>
        <x-components::table.row>
            <x-components::table.column>{{ __('Start') }}</x-components::table.column>
            @foreach (range(1,7) as $i)
                <x-components::table.column>
                    <x-components::form.radio-option name="options[start]" value="{{ $i }}"
                                                     :checked="old('options.start', isset($question) ? $question->options->get('start') : '') == $i"/>
                </x-components::table.column>
            @endforeach
        </x-components::table.row>
        <x-components::table.row>
            <x-components::table.column>{{ __('Target') }}</x-components::table.column>
            @foreach (range(1,7) as $i)
                <x-components::table.column>
                    <x-components::form.radio-option name="options[target]" value="{{ $i }}"
                                                     :checked="old('options.target', isset($question) ? $question->options->get('target') : '') == $i"/>
                </x-components::table.column>
            @endforeach
        </x-components::table.row>
    </x-components::table.body>
</x-components::table>

@include('questions._includes.excludeWeekends')
