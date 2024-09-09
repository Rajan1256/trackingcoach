<div class="text-xl font-bold">
    {{ isset($customer) ? replaceNames($answer->questionHistory->name, $customer) : $answer->questionHistory->name }}
</div>
@if ($answer->questionHistory->description)
    <p>
        {!! nl2br(e(isset($customer) ? replaceNames($answer->questionHistory->description, $customer) : $answer->questionHistory->description)) !!}
    </p>
@endif

@php
    $cols = 1;
    foreach (['target', 'zero', 'start'] as $field) {
        if (!is_null($answer->questionHistory->options->get($field, null))) {
            $cols++;
        }
    }

    if ($answer->helper->calculateDailyScore($answer) !== false) {
        $cols++;
    }
@endphp

<x-components::grid :cols="$cols" class="mt-5">
    <x-components::grid.block class="justify-self-center py-2">{{ __('Value') }}</x-components::grid.block>
    @if(!is_null($answer->questionHistory->options->get('target', null)))
        <x-components::grid.block class="justify-self-center py-2">{{ __('Target') }}</x-components::grid.block>
    @endif
    @if(!is_null($answer->questionHistory->options->get('zero', null)))
        <x-components::grid.block class="justify-self-center py-2">{{ __('Zero') }}</x-components::grid.block>
    @endif
    @if(!is_null($answer->questionHistory->options->get('start', null)))
        <x-components::grid.block class="justify-self-center py-2">{{ __('Start') }}</x-components::grid.block>
    @endif
    @if($answer->helper->calculateDailyScore($answer) !== false)
        <x-components::grid.block class="justify-self-center py-2">{{ __('Score') }}</x-components::grid.block>
    @endif
</x-components::grid>

<x-components::grid :cols="$cols" class="mb-20 bg-gray-100">
    <x-components::grid.block
            class="justify-self-center py-2">{{ $answer->helper->displayAnswer($answer) }}</x-components::grid.block>
    @if(!is_null($answer->questionHistory->options->get('target', null)))
        <x-components::grid.block
                class="justify-self-center py-2">{{ $answer->helper->getDayTargetString($answer) }}</x-components::grid.block>
    @endif
    @if(!is_null($answer->questionHistory->options->get('zero', null)))
        <x-components::grid.block
                class="justify-self-center py-2">{{ $answer->questionHistory->options->get('zero', null) }}</x-components::grid.block>
    @endif
    @if(!is_null($answer->questionHistory->options->get('start', null)))
        <x-components::grid.block
                class="justify-self-center py-2">{{ $answer->questionHistory->options->get('start', null) }}</x-components::grid.block>
    @endif
    @if($answer->helper->calculateDailyScore($answer) !== false)
        <x-components::grid.block class="justify-self-center py-2">
            @php($score = round($answer->helper->calculateDailyScore($answer)))
            <x-components::badges.basic
                    :color="scoreToColorClass($score)">
                {{ $score }}
            </x-components::badges.basic>
        </x-components::grid.block>
    @endif
</x-components::grid>
