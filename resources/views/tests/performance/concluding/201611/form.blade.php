<x-components::form.fieldset>
    <x-components::form.group>
        <x-components::form.label>{{ __('Date test') }}</x-components::form.label>
        <x-components::form.date value="{{ old('date', $model->date?->format('Y-m-d')) }}" name="date"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Introduction test') }}</x-components::form.label>
        <x-components::form.select name="introductionTest">
            @foreach ($customer->tests()->where('type', 'like', '%IntroductionPerformance%')->get() as $t)
                <x-components::form.select-option :value="$t->id"
                                                  :selected="old('introductionTest', (is_string($test->get('introductionTest')) ? $test->get('introductionTest') : $test->get('introductionTest')?->id)) == $t->id">
                    {{ $t->data->getName() }} &mdash; {{ $t->date->format('d-m-Y') }}</x-components::form.select-option>
            @endforeach
        </x-components::form.select>
    </x-components::form.group>
    <h2 class="text-xl">{{ __("Explanation personal goals") }}</h2>
    <x-components::form.group-top>
        <x-components::form.trix name="targetExplanation"
                                 id="targetExplanation">{{ old('targetExplanation', $test->get('targetExplanation')) }}</x-components::form.trix>
    </x-components::form.group-top>
    <h2 class="text-xl">{{ __("Findings of the rest measurement") }}</h2>
    <x-components::form.group>
        <x-components::form.label>{{ __('Weight (in kg)') }}</x-components::form.label>
        <x-components::form.input name="weight"
                                  value="{{ old('weight', $test->get('weight')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Systolic blood pressure') }}</x-components::form.label>
        <x-components::form.number name="systolic"
                                   min="1"
                                   value="{{ old('systolic', $test->get('systolic')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Diastolic blood pressure') }}</x-components::form.label>
        <x-components::form.number name="diastolic"
                                   min="1"
                                   value="{{ old('diastolic', $test->get('diastolic')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('HRV at rest without exercise') }}</x-components::form.label>
        <x-components::form.number min="1" name="hrvRestWithoutExercise"
                                   value="{{ old('hrvRestWithoutExercise', $test->get('hrvRestWithoutExercise')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('HR at rest without exercise') }}</x-components::form.label>
        <x-components::form.number min="1" name="hrRestWithoutExercise"
                                   value="{{ old('hrRestWithoutExercise', $test->get('hrRestWithoutExercise')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Breath frequency (per minute) without exercise') }}</x-components::form.label>
        <x-components::form.number min="1" name="respiratoryRate"
                                   value="{{ old('respiratoryRate', $test->get('respiratoryRate')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('HRV at rest with exercise') }}</x-components::form.label>
        <x-components::form.number min="1" name="hrvRestWithExercise"
                                   value="{{ old('hrvRestWithExercise', $test->get('hrvRestWithExercise')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('HR at rest with exercise') }}</x-components::form.label>
        <x-components::form.number min="1" name="hrRestWithExercise"
                                   value="{{ old('hrRestWithExercise', $test->get('hrRestWithExercise')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Breath frequency (per minute) with exercise') }}</x-components::form.label>
        <x-components::form.number min="1" name="respiratoryRateWithExercise"
                                   value="{{ old('respiratoryRateWithExercise', $test->get('respiratoryRateWithExercise')) }}"/>
    </x-components::form.group>
    <h3 class="text-lg">{{ __("Rest measurement without breathing exercise") }}</h3>
    <x-components::form.group>
        <x-components::form.label>{{ __('Graph') }}</x-components::form.label>
        <x-components::form.file name="rrAndHrRestWithoutExerciseImage"
                                 value="{{ old('rrAndHrRestWithoutExerciseImage', $test->get('rrAndHrRestWithoutExerciseImage')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Explanation') }}</x-components::form.label>
        <x-components::form.textarea name="rrAndHrRestWithoutExerciseExplanation"
                                     rows="5">{{ old('rrAndHrRestWithoutExerciseExplanation', $test->get('rrAndHrRestWithoutExerciseExplanation')) }}</x-components::form.textarea>
    </x-components::form.group>
    <h3 class="text-lg">{{ __("Rest measurement with breathing exercise") }}</h3>
    <x-components::form.group>
        <x-components::form.label>{{ __('Graph') }}</x-components::form.label>
        <x-components::form.file name="rrAndHrRestWithExerciseImage"
                                 value="{{ old('rrAndHrRestWithExerciseImage', $test->get('rrAndHrRestWithExerciseImage')) }}"/>
    </x-components::form.group>
    <x-components::form.group>
        <x-components::form.label>{{ __('Explanation') }}</x-components::form.label>
        <x-components::form.textarea name="rrAndHrRestWithExerciseExplanation"
                                     rows="5">{{ old('rrAndHrRestWithExerciseExplanation', $test->get('rrAndHrRestWithExerciseExplanation')) }}</x-components::form.textarea>
    </x-components::form.group>
    <h2 class="text-xl">{{ __("The numbers") }}</h2>
    <x-components::grid cols="5" class="gap-5">
        <x-components::grid.block>
            <x-components::form.group-top>
                <x-components::form.label>{{ __('Max. power') }}</x-components::form.label>
                <x-components::form.number name="wattMax" value="{{ old('wattMax', $test->get('wattMax')) }}"/>
            </x-components::form.group-top>
        </x-components::grid.block>
        <x-components::grid.block>
            <x-components::form.group-top>
                <x-components::form.label>{{ __('HR. AT') }}</x-components::form.label>
                <x-components::form.number name="hrAt" value="{{ old('hrAt', $test->get('hrAt')) }}"/>
            </x-components::form.group-top>
        </x-components::grid.block>
        <x-components::grid.block>
            <x-components::form.group-top>
                <x-components::form.label>{{ __('HR after cycling') }}</x-components::form.label>
                <x-components::form.number name="hrAfterCycling"
                                           value="{{ old('hrAfterCycling', $test->get('hrAfterCycling')) }}"/>
            </x-components::form.group-top>
        </x-components::grid.block>
        <x-components::grid.block>
            <x-components::form.group-top>
                <x-components::form.label>{{ __('HRV after cycling') }}</x-components::form.label>
                <x-components::form.number name="hrvAfterCycling"
                                           value="{{ old('hrvAfterCycling', $test->get('hrvAfterCycling')) }}"/>
            </x-components::form.group-top>
        </x-components::grid.block>
        <x-components::grid.block>
            <x-components::form.group-top>
                <x-components::form.label>{{ __('Power D2-zone') }}</x-components::form.label>
                <x-components::form.number name="wattD2" value="{{ old('wattD2', $test->get('wattD2')) }}"/>
            </x-components::form.group-top>
        </x-components::grid.block>
    </x-components::grid>
    <h2 class="text-xl">{{ __("Explanation results test") }}</h2>
    <x-components::form.group-top>
        <x-components::form.trix name="exerciseConclusions"
                                 id="exerciseConclusions">{{ old('exerciseConclusions', $test->get('exerciseConclusions')) }}</x-components::form.trix>
    </x-components::form.group-top>
    <h2 class="text-xl">{{ __("Explanation recovery") }}</h2>
    <x-components::form.group-top>
        <x-components::form.trix name="recoveryConclusions"
                                 id="recoveryConclusions">{{ old('recoveryConclusions', $test->get('recoveryConclusions')) }}</x-components::form.trix>
    </x-components::form.group-top>
    <h2 class="text-xl">{{ __("Advice") }}</h2>
    <x-components::form.group-top>
        <x-components::form.trix name="conclusion"
                                 id="conclusion">{{ old('conclusion', $test->get('conclusion')) }}</x-components::form.trix>
    </x-components::form.group-top>
    <h2 class="text-xl">{{ __("Person sports rest program") }}</h2>
    <x-components::form.group-top>
        <x-components::form.repeating :data="(old('personalProgram', $test->get('personalProgram', [])))"
                                      name="personalProgram">
            <x-components::form.textarea name="personalProgram[]"
                                         x-model="field.value"></x-components::form.textarea>
        </x-components::form.repeating>
    </x-components::form.group-top>
</x-components::form.fieldset>
