@if(count($growthData))
    <h2 class="text-2xl mb-5">{{ __("Your growth") }}</h2>

    <x-components::accordion>
        @foreach($growthData as $question)
            <x-components::accordion.item :title="$question->present()->model->name">
                <div id="growthFor{{$question->id}}" style="height: 175px;"></div>
                <div class="h-10"></div>
                @push('scripts')
                    <script type="text/javascript">
                        (function () {
                            google.charts.load('current', {'packages': ['corechart']});
                            google.charts.setOnLoadCallback(drawVisualization);

                            let timeout;
                            window.onresize = function () {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => {
                                    drawVisualization();
                                }, 100);
                            }

                            function drawVisualization() {
                                var data = google.visualization.arrayToDataTable([
                                    ['Week', 'Start', 'Target', 'Value', {role: 'style'}, {role: 'annotation'}],
                                        @foreach ($question->scores as $growthDataForQuestion)
                                    ['Week {{ $growthDataForQuestion->week }}', {{ to_real_float($growthDataForQuestion->extra_data['start']) }}, {{ to_real_float($growthDataForQuestion->extra_data['target']) }}, {{ to_real_float($growthDataForQuestion->extra_data['value']) }}, 'fill-color: {{ $growthDataForQuestion->color }}', {{ to_real_float(round($growthDataForQuestion->extra_data['value'] * 10) / 10) }}]@if(!$loop->last),@endif
                                    @endforeach
                                ]);

                                var options = {
                                    // legend: 'none',
                                    width: '100%',
                                    hAxis: {
                                        ticks: data.getDistinctValues(0),
                                        gridlines: {
                                            color: 'transparent',
                                            count: 0
                                        },
                                        textStyle: {color: 'black'},
                                        slantedText: false,
                                        slantedTextAngle: 45,
                                        baselineColor: '#6a6f7a',
                                        minorGridlines: {
                                            color: 'transparent',
                                            count: 0
                                        }
                                    }, vAxis: {
                                        baselineColor: '#6a6f7a',
                                        textStyle: {color: 'black'}
                                    },

                                    focusTarget: 'category',
                                    seriesType: 'bars',
                                    series: {
                                        0: {type: 'line', color: '#2f3542', lineDashStyle: [4, 6]},
                                        1: {type: 'line', color: '#70a1ff'}
                                    },
                                    chartArea: {
                                        left: 25,
                                        top: 25,
                                        width: '100%',
                                        height: 125
                                    }
                                };


                                var chart = new google.visualization.ComboChart(document.getElementById('growthFor{{ $question->id }}'));
                                chart.draw(data, options);
                            }
                        })();
                    </script>
                @endpush
            </x-components::accordion.item>
        @endforeach
    </x-components::accordion>
@endif
