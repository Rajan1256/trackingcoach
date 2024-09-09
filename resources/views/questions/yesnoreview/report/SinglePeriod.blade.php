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
            orientation: 'vertical',
            chartArea: {width: '75%'},
            seriesType: 'bars',
            hAxis: {
                ticks: [{v: 0, f: 'No'}, {v: 50, f: '50%'}, {v: 100, f: 'Yes'}],
                minValue: 0,
                maxValue: 100,
                baseline: 50
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
                    @if ($answersSelf->count())
                ['{{ __("Self") }}', {{ number_format($answersSelf->average('answer_boolean') * 100, 2, '.', '') }}],
                    @endif
                    @if($answersOthers->count())
                ['{{ __("Others") }}', {{ number_format($answersOthers->average('answer_boolean') * 100, 2, '.', '') }}]
                @endif
            ]);

            dd{{ $rand }} = google.visualization.arrayToDataTable([
                ['{{ __("Type") }}', '{{ __("Score") }}', {type: 'string', role: 'annotation'}],
                    @if ($answersSelf->count())
                ['{{ __("Self") }}', {{ intval($answersSelf->average('answer_boolean') * 100) }}, null],
                    @endif
                    @foreach($answersOthers->sortByDesc('answer_boolean')->unique('answer_boolean') as $answerOther)
                ['{{ trans_choice(":count others", $answersOthers->where('answer_boolean', $answerOther->answer_boolean)->count()) }}', {{ intval($answerOther->answer_boolean) * 100 }}, '{{ $answersOthers->where('answer_boolean', $answerOther->answer_boolean)->count() }}x'],
                @endforeach
            ]);


            c{{ $rand }} = (new google.visualization.ComboChart(document.getElementById('{{ $rand }}')));
            c{{ $rand }}.draw(d{{ $rand }}, o{{ $rand }});
        }
    </script>
@endpush
