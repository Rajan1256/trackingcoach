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

<div class="col-span-6">
    {{ __('Scores for this question will be generated daily.') }}
    <br/>
    <br/>
    {{ __("The zero value is required to calculate the score and is the value on which the client will score 0%.") }}
    <br/>
    {{ __("The calculation is as follows") }}: &nbsp; <code>SCORE = 100 / (<em>{{ __("target") }}</em> -
        <em>{{ __("zero") }}</em>) * (<em>{{ __("question input") }}</em> - <em>{{ __("zero") }}</em>)</code>
    <br/><br/>
    {{ __("For example, if the target value is 20 and the zero value is 10, the client will score 50% if the answer is 15.") }}
    (
    <code>100 / (20 - 10) * (15 - 10)</code> = 50)<br/>
    {{ __("Another example: If the client has a target weight of 80kg and the zero value is 90kg, the client will score 10% if the weight is 89kg.") }}
    (
    <code>100 / (80 - 90) * (89 - 90)</code> = 10)<br/>
    <br/>
    {{ __("For more simple questions, like \"How many minutes did you read today?\", the zero value can be set to '0'. The user will score 0% for 0 minutes. Let's say the target is 60 minutes, and the input is 30, the score will be 50.") }}
    (
    <code>100 / (60 - 0) * (30 - 0)</code> = 50)
</div>


@include('questions._includes.excludeWeekends')
