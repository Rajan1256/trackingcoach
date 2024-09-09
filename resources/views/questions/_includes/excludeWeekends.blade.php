@php($settings = $customer->getSettings())
@if ( ! isset($customer) || $settings->days_per_week == 7)
    <x-components::form.group-top>
        <x-components::form.label :small="false">
            {{ __("Weekends") }}
        </x-components::form.label>

        <x-components::form.checkbox>
            <x-components::form.checkbox-option
                    value="1"
                    name="options[excludeWeekends]"
                    :checked="old('options.excludeWeekends', isset($question) ? intval($question->options->get('excludeWeekends')) : '') == 1">{{ __("Do not ask this question during the weekend") }}</x-components::form.checkbox-option>
        </x-components::form.checkbox>
    </x-components::form.group-top>

@endif
