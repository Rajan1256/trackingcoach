@php($rand = rand(100000, 9999999999))
@can('create', App\Models\Review::class)
    <x-components::buttons.button
            onclick="toggleD{{ $rand }}()">{{ __("Toggle individual results") }}</x-components::buttons.button>
@endcan
<div class="cb"></div>
<div id="{{ $rand }}" style="height: 300px;"></div>

@push('scripts')
    <script>
        var c{{ $rand }}, d{{ $rand }}, dd{{ $rand }}, o{{ $rand }}, exp{{ $rand }} = false;
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(f{{ $rand }});

        var o{{$rand}} = {
            colors: ['#f9c80b', '#000000'],
            chartArea: {width: '75%'},
            chartArea: {height: '50%'},
            seriesType: 'bars',
            orientation: 'vertical',
            hAxis: {
                ticks: [
                    @foreach (range(-3,3) as $i)
                            {{ "{" }}
                            {!! "v:" .$i . ", f: '" . $i . "\\n" . trans('trackingcoach.questions.options.' . \App\Questions\ZeroCenteredSevenScale::$scaleTypes[$answers->first()->question->options->get('scaleType', 'effectiveness')][$i+ 3]) . "'" !!}
                            {{ "}," }}
                            @endforeach
                ],
                minValue: -3,
                maxValue: 3,
                gridlines: {count: 7}
            },
            legend: {position: "none"}
        };

        function toggleD{{ $rand }}() {
            if (!exp{{ $rand }}) {
                var view = new google.visualization.DataView(dd{{ $rand }});
                c{{ $rand }}.draw(view, o{{ $rand }});

                exp{{ $rand }} = true;
            } else if (exp{{ $rand }}) {
                var view = new google.visualization.DataView(d{{ $rand }});
                c{{ $rand }}.draw(view, o{{ $rand }});

                exp{{ $rand }} = false;
            }
        }

        let timeout{{ $rand }};
        window.onresize = function () {
            clearTimeout(timeout{{ $rand }});
            timeout{{ $rand }} = setTimeout(() => {
                f{{ $rand }}();
            }, 100);
        }

        function f{{ $rand }}() {

            d{{ $rand }} = google.visualization.arrayToDataTable([
                ['{{ __("Type") }}', '{{ __("Average score") }}'],
                    @if ($answersSelf->count() )
                ['{{ __("Self") }}', {{ number_format($answersSelf->average('answer_number'), 2, '.', '') }}],
                    @endif
                    @if ($answersOthers->count() )
                ['{{ __("Others") }}', {{ number_format($answersOthers->average('answer_number'), 2, '.', '') }}]
                @endif
            ]);

            dd{{ $rand }} = google.visualization.arrayToDataTable([
                ['{{ __("Type") }}', '{{ __("Score") }}', {type: 'string', role: 'annotation'}],
                    @if ($answersSelf->count() )
                ['{{ __("Self") }}', {{ number_format($answersSelf->average('answer_number'), 2, '.', '') }}, null],
                    @endif
                    @foreach($answersOthers->sortByDesc('answer_number')->unique('answer_number') as $answerOther)
                ['{{ trans_choice(":count others", $answersOthers->where('answer_number', $answerOther->answer_number)->count()) }}', {{ intval($answerOther->answer_number) }}, '{{ $answersOthers->where('answer_number', $answerOther->answer_number)->count() }}x'],
                @endforeach
            ]);


            c{{ $rand }} = (new google.visualization.ComboChart(document.getElementById('{{ $rand }}')));
            c{{ $rand }}.draw(d{{ $rand }}, o{{ $rand }});
        }
    </script>
@endpush
