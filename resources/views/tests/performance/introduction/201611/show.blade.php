<div class="bg-gray-500 text-white p-3">
    <h1 class="text-3xl text-center font-bold">{!! $test->helper->getName() !!}</h1>
</div>
<div class="mt-3 mb-14">
    <h2 class="text-2xl">{{ $customer->name }}</h2>
    <h3 class="text-xl">{{ date_format_helper($test->date)->get_dmy() }}</h3>
</div>

<div class="bg-gray-500 text-white p-3">
    <h1 class="text-3xl font-bold">1. {{ __('Background') }}</h1>
</div>
<p class="my-3">
    {{ __("You are going to follow a TopMind ™ Training. A TopMind Test is done at the beginning of this program. In this test we measure how fit you are, both at rest and during exercise. Together we formulate your personal goals and objectives for this program. At the end of the program there will be another test to determine to what extent your fitness has improved.") }}
</p>

<x-components::accordion class="border border-gray-200 sm:rounded-none shadow-none mb-14">
    <x-components::accordion.item :title="__('The personal goals and objectives')"
                                  class="border-none">
        <x-components::list ol>
            @if($test->personalTargets)
                @foreach($test->personalTargets as $li)
                    <x-components::lists.item>{!! nl2br(e($li)) !!}</x-components::lists.item>
                @endforeach
            @else
                <x-components::lists.item>{{ __('No personal targets') }}</x-components::lists.item>
            @endif
        </x-components::list>
    </x-components::accordion.item>
</x-components::accordion>


<div class="bg-gray-500 text-white p-3">
    <h1 class="text-3xl font-bold">2. {{ __("Findings of the rest measurement") }}</h1>
</div>
<p class="my-3">
    {!! __("The extent of stress determines a large part of how you feel and how your body functions. In the resting measurement we have determined your stress level based on your heart rhythm (:HRVLINKOPEN:HRV:HRVLINKCLOSE:). By breathing more slowly you increase the variation in your heart rhythm, you become more relaxed. There is more harmony between heart, body and brain.", ['HRVLINKOPEN:' => '<span class="font-bold underline !text-blue-600" title="'.__("The time between consecutive heart beats varies, this is known as Heart Rate Variability (HRV). HRV gives important information about the functioning of the autonomic nervous system. Research shows that a high HRV is related to a higher level of fitness and relaxation, while a low HRV is related to fatigue and stress.").'" class="uk-link uk-text-bold">', 'HRVLINKCLOSE:' => '</span>']) !!}
</p>
<p class="my-3">
    {{ __("A good weight and good blood pressure are important for your health.") }}
</p>

<x-components::accordion class="border border-gray-200 sm:rounded:none shadow-none mb-14">
    <x-components::accordion.item :title="__('BMI')" itemClasses="px-8">
        <x-components::table>
            <x-components::table.body>
                <x-components::table.row>
                    <x-components::table.heading>{{ __('Length') }}</x-components::table.heading>
                    <x-components::table.column>
                        {{ $test->length ? number_format($test->length / 100, 2, ',', '') : '?' }} m
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.heading>{{ __('Weight') }}</x-components::table.heading>
                    <x-components::table.column>
                        {{ $test->weight ?: '?' }} kg
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    @php
                        if ( ! $test->length || ! $test->weight )
                            $bmi = null;
                        else {
                            $bmi = floatval(str_replace(',', '.', $test->weight)) / ( ($test->length / 100) * ($test->length / 100) );
                        }
                    @endphp
                    <x-components::table.heading>{!! __("Your :BMILINKOPEN:BMI:BMILINKCLOSE: is", ['BMILINKOPEN:' => '<span class="font-bold underline !text-blue-600" title="'.__("BMI means Body Mass Index and is the ratio between weight and length (BMI=W/L<sup>2</sup>). The BMI is widely used to give an indication of whether there is excess weight (BMI greater than 25) or underweight (BMI less than 18.5).").'">', 'BMILINKCLOSE:' => '</span>']) !!}</x-components::table.heading>
                    <x-components::table.column :class="($bmi < 18.5 || $bmi > 25) ? '!text-red-600' : ''">
                        {{ $bmi ? number_format($bmi, 1, ',', '.') : '?' }}
                    </x-components::table.column>
                </x-components::table.row>
            </x-components::table.body>
        </x-components::table>
        @if ( $bmi )
            <p class="mb-5">
                @if($bmi < 18.5)
                    {{ __("You are underweight") }}
                @elseif($bmi < 25)
                    {{ __("Your weight is good") }}
                @elseif($bmi < 30)
                    {{ __("You are overweight") }}
                @else
                    {{ __("You are seriously overweight") }}
                @endif
            </p>
        @endif
    </x-components::accordion.item>
    <x-components::accordion.item :title="__('Blood pressure')" itemClasses="px-8">
        <x-components::table>
            <x-components::table.header>
                <x-components::table.heading></x-components::table.heading>
                <x-components::table.heading>{{ __('Results') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Norm') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Findings') }}</x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                <x-components::table.row>
                    <x-components::table.column>{{ __('Systolic blood pressure') }}</x-components::table.column>
                    <x-components::table.column
                            :class="($test->systolic > 130) ? '!text-red-600' : ''">{{ $test->systolic }}</x-components::table.column>
                    <x-components::table.column>&le; 130</x-components::table.column>
                    <x-components::table.column>
                        @if($test->systolic <= 130 )
                            {{ __("Your Systolic blood pressure is good") }}
                        @elseif($test->systolic <= 140)
                            {{ __("Your Systolic blood pressure is on the high side") }}
                        @else
                            {{ __("Your Systolic blood pressure in too high") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __('Diastolic blood pressure') }}</x-components::table.column>
                    <x-components::table.column
                            :class="($test->diastolic > 85) ? '!text-red-600' : ''">{{ $test->diastolic }}</x-components::table.column>
                    <x-components::table.column>&le; 85</x-components::table.column>
                    <x-components::table.column>
                        @if($test->diastolic <= 85 )
                            {{ __("Your Diastolic blood pressure is good") }}
                        @elseif($test->diastolic <= 90)
                            {{ __("Your Diastolic blood pressure in on the high side") }}
                        @else
                            {{ __("Your Diastolic blood pressure is too high") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
            </x-components::table.body>
        </x-components::table>
    </x-components::accordion.item>
    <x-components::accordion.item :title="__('Rest measurement without breathing exercise')" itemClasses="px-8">
        @if ( $test->getMedia('rrAndHrRestWithoutExerciseImage')->first() )
            <x-components::grid cols="3" class="mb-14 gap-3">
                <x-components::grid.block colSpan="2">
                    <img src="{{ route('customers.tests.media', ['customer' => $customer, 'test' => $test, 'media' => $test->getFirstMedia('rrAndHrRestWithoutExerciseImage')]) }}"
                         alt="" style="width: 100%">
                    <p class="text-sm">
                        {!! __(":REDOPEN:Red:REDCLOSE: is the heart rhythm and at the same time an indication for your breathing pattern, :BLUEOPEN:blue:BLUECLOSE: is your heart rate.", ["REDOPEN:" => '<span class="font-bold !text-red-600">', "REDCLOSE:" => '</span>',"BLUEOPEN:" => '<span class="font-bold !text-blue-600">', "BLUECLOSE:" => '</span>']) !!}
                    </p>
                </x-components::grid.block>
                <x-components::grid.block>
                    {!! nl2br(e($test->rrAndHrRestWithoutExerciseExplanation)) !!}
                </x-components::grid.block>
            </x-components::grid>
        @endif
        <x-components::table>
            <x-components::table.header>
                <x-components::table.heading></x-components::table.heading>
                <x-components::table.heading>{{ __('Results') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Norm') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Findings') }}</x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                <x-components::table.row>
                    <x-components::table.column>
                        <span title="{{ __("The time between consecutive heart beats varies, this is known as Heart Rate Variability (HRV). HRV gives important information about the functioning of the autonomic nervous system. Research shows that a high HRV is related to a higher level of fitness and relaxation, while a low HRV is related to fatigue and stress.") }}"
                              class="font-bold underline !text-blue-600">{{ __("HRV") }}</span>
                    </x-components::table.column>
                    <x-components::table.column
                            :class="($test->hrvRestWithoutExercise < 50) ? '!text-red-600' : ''">{{ $test->hrvRestWithoutExercise }}</x-components::table.column>
                    <x-components::table.column>&ge; 50</x-components::table.column>
                    <x-components::table.column>
                        @if ( $test->hrvRestWithoutExercise <= 29)
                            {{ __("Your HRV is largely inadequate") }}
                        @elseif ($test->hrvRestWithoutExercise <= 49)
                            {{ __("Your HRV is inadequate") }}
                        @else
                            {{ __("Your HRV is good") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("Heart rate") }}</x-components::table.column>
                    <x-components::table.column
                            :class="($test->hrRestWithoutExercise > 75) ? '!text-red-600' : ''">{{ $test->hrRestWithoutExercise }}</x-components::table.column>
                    <x-components::table.column>&le; 75</x-components::table.column>
                    <x-components::table.column>
                        @if ($test->hrRestWithoutExercise <= 75)
                            {{ __("Your heart rate is good") }}
                        @else
                            {{ __("Your heart rate is too high") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("Breating rate") }}</x-components::table.column>
                    <x-components::table.column
                            :class="($test->respiratoryRate > 10) ? '!text-red-600' : ''">{{ $test->respiratoryRate }}</x-components::table.column>
                    <x-components::table.column>&le; 10</x-components::table.column>
                    <x-components::table.column>
                        @if ($test->respiratoryRate <= 10)
                            {{ __("Your breath frequency is good") }}
                        @elseif ($test->respiratoryRate <= 13)
                            {{ __("Your breath frequency could be better") }}
                        @else
                            {{ __("Your breath frequency is too high") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
            </x-components::table.body>
        </x-components::table>
    </x-components::accordion.item>
    <x-components::accordion.item :title="__('Rest measurement with breathing exercise')" itemClasses="px-8">
        @if ( $test->getMedia('rrAndHrRestWithExerciseImage')->first() )
            <x-components::grid cols="3" class="mb-14 gap-3">
                <x-components::grid.block colSpan="2">
                    <img src="{{ route('customers.tests.media', ['customer' => $customer, 'test' => $test, 'media' => $test->getFirstMedia('rrAndHrRestWithExerciseImage')]) }}"
                         alt="" style="width: 100%">
                    <p class="text-sm">
                        {!! __(":REDOPEN:Red:REDCLOSE: is the heart rhythm and at the same time an indication for your breathing pattern, :BLUEOPEN:blue:BLUECLOSE: is your heart rate.", ["REDOPEN:" => '<span class="font-bold !text-red-600">', "REDCLOSE:" => '</span>',"BLUEOPEN:" => '<span class="font-bold !text-blue-600">', "BLUECLOSE:" => '</span>']) !!}
                    </p>
                </x-components::grid.block>
                <x-components::grid.block>
                    {!! nl2br(e($test->rrAndHrRestWithExerciseExplanation)) !!}
                </x-components::grid.block>
            </x-components::grid>
        @endif
        <x-components::table>
            <x-components::table.header>
                <x-components::table.heading></x-components::table.heading>
                <x-components::table.heading>{{ __('Results') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Norm') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Findings') }}</x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                <x-components::table.row>
                    <x-components::table.column>
                        <span title="{{ __("The time between consecutive heart beats varies, this is known as Heart Rate Variability (HRV). HRV gives important information about the functioning of the autonomic nervous system. Research shows that a high HRV is related to a higher level of fitness and relaxation, while a low HRV is related to fatigue and stress.") }}"
                              class="font-bold underline !text-blue-600">{{ __("HRV") }}</span>
                    </x-components::table.column>
                    <x-components::table.column
                            :class="($test->hrvRestWithExercise < 50) ? '!text-red-600' : ''">{{ $test->hrvRestWithExercise }}</x-components::table.column>
                    <x-components::table.column>&ge; 50</x-components::table.column>
                    <x-components::table.column>
                        @if ( $test->hrvRestWithExercise <= 29)
                            {{ __("Your HRV is largely inadequate") }}
                        @elseif ($test->hrvRestWithExercise <= 49)
                            {{ __("Your HRV is inadequate") }}
                        @else
                            {{ __("Your HRV is good") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("Heart rate") }}</x-components::table.column>
                    <x-components::table.column
                            :class="($test->hrRestWithExercise > 75) ? '!text-red-600' : ''">{{ $test->hrRestWithExercise }}</x-components::table.column>
                    <x-components::table.column>&le; 75</x-components::table.column>
                    <x-components::table.column>
                        @if ($test->hrRestWithExercise <= 75)
                            {{ __("Your heart rate is good") }}
                        @else
                            {{ __("Your heart rate is too high") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("Breating rate") }}</x-components::table.column>
                    <x-components::table.column
                            :class="($test->respiratoryRateWithExercise > 10) ? '!text-red-600' : ''">{{ $test->respiratoryRateWithExercise }}</x-components::table.column>
                    <x-components::table.column>&le; 10</x-components::table.column>
                    <x-components::table.column>
                        @if ($test->respiratoryRateWithExercise <= 10)
                            {{ __("Your breath frequency is good") }}
                        @elseif ($test->respiratoryRateWithExercise <= 13)
                            {{ __("Your breath frequency could be better") }}
                        @else
                            {{ __("Your breath frequency is too high") }}
                        @endif
                    </x-components::table.column>
                </x-components::table.row>
            </x-components::table.body>
        </x-components::table>
    </x-components::accordion.item>
</x-components::accordion>

<div class="bg-gray-500 text-white p-3">
    <h1 class="text-3xl font-bold">3. {{ __("The exercise test") }}</h1>
</div>
<p class="my-3">
    {!!  __("An exercise test provides insight into your physical fitness. Important values for this are your maximum power and :VO2OPEN:VO<sub>2</sub>max/kg:VO2CLOSE:. If your body can absorb inhaled oxygen more efficiently and use it better for your muscle, your maximum power will be better.", ['VO2OPEN:' => '<span class="font-bold underline !text-blue-600" title="'.__("VO2max/kg is an important measure of your performance during exercise. VO2max/kg is the maximum oxygen uptake capacity that the body can absorb and use per kg body weight").'">', 'VO2CLOSE:' => '</span>']) !!}
</p>
<p class="my-3">
    {{ __("This also has a direct advantage in your daily live. With higher maximum power you will be tired less quickly.") }}
</p>
<p class="my-3">
    {{ __("By exercising you can improve your physical fitness. In addition, exercising is very effective for relaxing and reducing the physical consequences of stress.") }}
</p>

<x-components::accordion>
    <x-components::accordion.item :title="__('Results exercise test')" itemClasses="px-8">
        <x-components::table>
            <x-components::table.body>
                <x-components::table.row>
                    <x-components::table.column
                            :title="__('VO2max/kg is an important measure of your performance during exercise. VO2max/kg is the maximum oxygen uptake capacity that the body can absorb and use per kg body weight')">
                        {!! __("VO<sub>2</sub>max/kg") !!}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $test->data->vo2Max() ?? '?' }}
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>
                        {{ __("maximum power (Watt)") }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $test->wattMax ?? '?' }}
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column
                            :title="__('Maximum heart rate is the maximum heart rate measured during the exercise test. The value is not the maximum that the heart can handle but is determined by the cooperation between lungs, heart and muscles. The value depends on the structure of the exercise test and which sport is being practiced. With a running test the maximum heart rate is higher than with a bicycle test. In this case, the course of heart rate and power is determined with a bicycle test. For the heart this is a submaximal exercise test.')">
                        {{ __("maximum heart rate") }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $test->hrMax ?? '?' }}
                    </x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column
                            :title="__('The anaerobic threshold is the moment at which the body acidifies.')">
                        {{ __("heart rate at anaerobic threshold") }}
                    </x-components::table.column>
                    <x-components::table.column>
                        {{ $test->hrAt ?? '?' }}
                    </x-components::table.column>
                </x-components::table.row>
            </x-components::table.body>
        </x-components::table>
    </x-components::accordion.item>
    <x-components::accordion.item itemClasses="px-8"
                                  :title="__('Compare your VO<sub>2</sub>max/kg with other :MENORWOMEN:', ['MENORWOMEN:' => $test->tableType == 'men' ? __('men') : __('women')])">
        <x-components::table>
            <x-components::table.header>
                <x-components::table.heading>{{ $test->tableType == 'men' ? ucfirst(__("men")) : ucfirst(__("women")) }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Very bad') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Bad') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Reasonable') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Good') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Very good') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Excellent') }}</x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                @if ($test->tableType == 'men')
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 13, 'to:' => 19]) }}</x-components::table.column>
                        <x-components::table.column>&lt;35.0</x-components::table.column>
                        <x-components::table.column>35.0 - 42.5</x-components::table.column>
                        <x-components::table.column>42.6 - 50.1</x-components::table.column>
                        <x-components::table.column>50.2 - 57.7</x-components::table.column>
                        <x-components::table.column>57.8 - 65.3</x-components::table.column>
                        <x-components::table.column>&gt;65.3</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 20, 'to:' => 29]) }}</x-components::table.column>
                        <x-components::table.column>&lt;33.0</x-components::table.column>
                        <x-components::table.column>33.0 - 40.0</x-components::table.column>
                        <x-components::table.column>40.1 - 47.1</x-components::table.column>
                        <x-components::table.column>47.2 - 54.2</x-components::table.column>
                        <x-components::table.column>54.3 - 61.3</x-components::table.column>
                        <x-components::table.column>&gt;61.3</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 30, 'to:' => 39]) }}</x-components::table.column>
                        <x-components::table.column>&lt;31.5</x-components::table.column>
                        <x-components::table.column>31.5 - 38.5</x-components::table.column>
                        <x-components::table.column>38.6 - 45.6</x-components::table.column>
                        <x-components::table.column>45.7 - 52.7</x-components::table.column>
                        <x-components::table.column>52.8 - 59.8</x-components::table.column>
                        <x-components::table.column>&gt;59.8</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 40, 'to:' => 49]) }}</x-components::table.column>
                        <x-components::table.column>&lt;30.2</x-components::table.column>
                        <x-components::table.column>30.2 - 37.2</x-components::table.column>
                        <x-components::table.column>37.3 - 44.3</x-components::table.column>
                        <x-components::table.column>44.4 - 51.4</x-components::table.column>
                        <x-components::table.column>51.5 - 58.5</x-components::table.column>
                        <x-components::table.column>&gt;58.5</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 50, 'to:' => 59]) }}</x-components::table.column>
                        <x-components::table.column>&lt;26.1</x-components::table.column>
                        <x-components::table.column>26.1 - 33.1</x-components::table.column>
                        <x-components::table.column>33.2 - 40.2</x-components::table.column>
                        <x-components::table.column>40.3 - 47.3</x-components::table.column>
                        <x-components::table.column>47.4 - 54.4</x-components::table.column>
                        <x-components::table.column>&gt;54.5</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>60+</x-components::table.column>
                        <x-components::table.column>&lt;20.5</x-components::table.column>
                        <x-components::table.column>20.5 - 28.5</x-components::table.column>
                        <x-components::table.column>28.6 - 36.6</x-components::table.column>
                        <x-components::table.column>36.7 - 44.7</x-components::table.column>
                        <x-components::table.column>44.8 - 52.8</x-components::table.column>
                        <x-components::table.column>&gt;52.8</x-components::table.column>
                    </x-components::table.row>
                @else
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 13, 'to:' => 19]) }}</x-components::table.column>
                        <x-components::table.column>&lt;25.0</x-components::table.column>
                        <x-components::table.column>25.0 - 31.5</x-components::table.column>
                        <x-components::table.column>31.6 - 38.1</x-components::table.column>
                        <x-components::table.column>38.2 - 44.7</x-components::table.column>
                        <x-components::table.column>44.8 - 51.3</x-components::table.column>
                        <x-components::table.column>&gt;51.3</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 20, 'to:' => 29]) }}</x-components::table.column>
                        <x-components::table.column>&lt;23.6</x-components::table.column>
                        <x-components::table.column>23.6 - 30.1</x-components::table.column>
                        <x-components::table.column>30.2 - 36.7</x-components::table.column>
                        <x-components::table.column>36.8 - 43.3</x-components::table.column>
                        <x-components::table.column>43.4 - 49.9</x-components::table.column>
                        <x-components::table.column>&gt;49.9</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 30, 'to:' => 39]) }}</x-components::table.column>
                        <x-components::table.column>&lt;22.8</x-components::table.column>
                        <x-components::table.column>22.8 - 29.3</x-components::table.column>
                        <x-components::table.column>29.4 - 35.9</x-components::table.column>
                        <x-components::table.column>36.0 - 42.5</x-components::table.column>
                        <x-components::table.column>42.6 - 49.1</x-components::table.column>
                        <x-components::table.column>&gt;49.1</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 40, 'to:' => 49]) }}</x-components::table.column>
                        <x-components::table.column>&lt;21.0</x-components::table.column>
                        <x-components::table.column>21.0 - 27.5</x-components::table.column>
                        <x-components::table.column>27.6 - 34.1</x-components::table.column>
                        <x-components::table.column>34.2 - 40.7</x-components::table.column>
                        <x-components::table.column>40.8 - 47.3</x-components::table.column>
                        <x-components::table.column>&gt;47.3</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>{{ __(':from:-:to: year', ['from:' => 50, 'to:' => 59]) }}</x-components::table.column>
                        <x-components::table.column>&lt;20.2</x-components::table.column>
                        <x-components::table.column>20.2 - 26.7</x-components::table.column>
                        <x-components::table.column>26.8 - 33.3</x-components::table.column>
                        <x-components::table.column>33.4 - 39.9</x-components::table.column>
                        <x-components::table.column>40.0 - 46.5</x-components::table.column>
                        <x-components::table.column>&gt;46.5</x-components::table.column>
                    </x-components::table.row>
                    <x-components::table.row>
                        <x-components::table.column>60+</x-components::table.column>
                        <x-components::table.column>&lt;17.5</x-components::table.column>
                        <x-components::table.column>17.5 - 23.5</x-components::table.column>
                        <x-components::table.column>23.6 - 29.6</x-components::table.column>
                        <x-components::table.column>29.7 - 35.7</x-components::table.column>
                        <x-components::table.column>35.8 - 41.8</x-components::table.column>
                        <x-components::table.column>&gt;41.8</x-components::table.column>
                    </x-components::table.row>
                @endif
            </x-components::table.body>
        </x-components::table>
    </x-components::accordion.item>
    <x-components::accordion.item itemClasses="px-8"
                                  :title="__('Heart rate zones for endurance training')">
        @php($r = intval($test->hrAt) ?: 0.1)
        <x-components::table>
            <x-components::table.header>
                <x-components::table.heading>{{ __('Heart rate zones') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Cycling / rowing') }}</x-components::table.heading>
                <x-components::table.heading>{{ __('Running / crosstraining') }}</x-components::table.heading>
            </x-components::table.header>
            <x-components::table.body>
                <x-components::table.row>
                    <x-components::table.column>{{ __("warming-up zone") }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.7*$r) }} - {{ round(0.725*$r) }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.7*($r+10)) }}
                        - {{ round(0.725*($r+10)) }}</x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("D1-zone") }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.725*$r) }} - {{ round(0.8*$r) }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.725*($r+10)) }}
                        - {{ round(0.8*($r+10)) }}</x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("D2-zone") }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.8*$r) }} - {{ round(0.9*$r) }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.8*($r+10)) }}
                        - {{ round(0.9*($r+10)) }}</x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("D3-zone") }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.9*$r) }} - {{ $r }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.9*($r+10)) }} - {{ ($r+10) }}</x-components::table.column>
                </x-components::table.row>
                <x-components::table.row>
                    <x-components::table.column>{{ __("Cooling-down zone") }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.7*$r+5) }}
                        - {{ round(0.725*$r+5) }}</x-components::table.column>
                    <x-components::table.column>{{ round(0.7*($r+10)+5) }}
                        - {{ round(0.725*($r+10)+5) }}</x-components::table.column>
                </x-components::table.row>
            </x-components::table.body>
        </x-components::table>
    </x-components::accordion.item>
</x-components::accordion>

<div class="bg-gray-500 text-white p-3 mt-8">
    <h1 class="text-3xl font-bold">4. {{ __("Advice") }}</h1>
</div>

<x-components::accordion class="mt-8">
    <x-components::accordion.item itemClasses="px-8" :title="__('The advice')">
        <div class="mb-8">{!! $test->conclusion !!}</div>
    </x-components::accordion.item>
    <x-components::accordion.item itemClasses="px-8" :title="__('Your personal Sportrusten-Program')">
        <x-components::list ol class="mb-8">
            @if($test->personalProgram)
                @foreach($test->personalProgram as $li)
                    <x-components::lists.item :truncate="false">{!! nl2br(e($li)) !!}</x-components::lists.item>
                @endforeach
            @else
                <x-components::lists.item>{{ __('No personal program') }}</x-components::lists.item>
            @endif
        </x-components::list>
    </x-components::accordion.item>
</x-components::accordion>
