<x-components::form.group-top>
    <x-components::form.label :small="false">{{ __("Score generation method") }}</x-components::form.label>
    <x-components::form.select name="options[score_generation_method]">
        @foreach(\App\Questions\NumericWeekly::$generationMethods as $methodName => $methodTranslation)
            <x-components::form.select-option value="{{ $methodName }}"
                                              :selected="old('options.score_generation_method', isset($question) ? $question->options->get('score_generation_method') : '') == $methodName">
                {{ __($methodTranslation) }}
            </x-components::form.select-option>
        @endforeach
    </x-components::form.select>
</x-components::form.group-top>

<div class="col-span-6">
    {{ __("Select a score generation method. A short explanation for all options:") }} <br/>
    <x-components::list ol>
        <x-components::lists.item :truncate="false">
            <strong>{{ __("Sum of all values meets weekly target") }}
                :</strong> {{ __("All user inputs are added and will be compared to the weekly target and zero value.") }}
        </x-components::lists.item>
        <x-components::lists.item :truncate="false">
            <strong>{{ __("Average value meets target") }}
                :</strong> {{ __("The average value will be compared to the daily target and zero value. This option is different from option 1: If a client skips one or more days, choosing this option will not affect their score.") }}
        </x-components::lists.item>
        <x-components::lists.item :truncate="false">
            <strong>{{ __("Lowest value meets target") }}
                :</strong> {{ __("The lowest daily value will be used to calculate the weekly score. An example question would be \"What was your weight today?\". The weekly score will be generated based on the lowest weight for that week.") }}
        </x-components::lists.item>
        <x-components::lists.item :truncate="false">
            <strong>{{ __("Highest value meets target") }}
                :</strong> {{ __("The highest daily value will be used to calculate the weekly score. Other than that, calculation will be the same as option 3.") }}
        </x-components::lists.item>
    </x-components::list>
</div>

<x-components::form.group-top>
    <x-components::form.label :small="false">
        {{ __("Target value") }}
    </x-components::form.label>
    <x-components::form.input name="options[target]" id="target"
                              :value="old('options.target', isset($question) ? $question->options->get('target') : '')"/>
</x-components::form.group-top>

<x-components::form.group-top>
    <x-components::form.label :small="false">
        {{ __("Zero value") }}
    </x-components::form.label>
    <x-components::form.input name="options[zero]" id="zero"
                              :value="old('options.zero', isset($question) ? $question->options->get('zero') : '')"/>
</x-components::form.group-top>

<x-components::form.group-top>
    <x-components::form.label :small="false">
        {{ __("Start value") }}
    </x-components::form.label>
    <x-components::form.input name="options[start]" id="start"
                              :value="old('options.start', isset($question) ? $question->options->get('start') : '')"/>
</x-components::form.group-top>

@include('questions._includes.excludeWeekends')
