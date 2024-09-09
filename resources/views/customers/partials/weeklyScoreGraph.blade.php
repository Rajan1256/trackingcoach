<h2 class="text-2xl mb-5">{{ __("Weekly scores") }}</h2>


@if ($lastWeeks->count())
    <div class="cb h20"></div>
    <div id="chart" style="height: 400px;"></div>

    @push('scripts')
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawWeeklyScoreGraph);

            let timeout;
            window.addEventListener('resize', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    drawWeeklyScoreGraph();
                }, 100);
            });

            function drawWeeklyScoreGraph() {
                var data = google.visualization.arrayToDataTable([
                    ['{{ __('Week number') }}', '{{ __('Average score') }}', {role: 'certainty'}, '{{ __('Moving average (:count weeks)', ['count' => 4]) }}', {role: 'certainty'}]

                    @foreach ($lastWeeks as $week)
                    , ['Week {{ $week['date']->format('W') }}', {{ $week['score'] }}, {{ $week['noWeekResponses'] >= 3 ? 'true' : 'false' }}, {{ $week['movingAverage'] }}, {{ $week['movingAverageComplete'] ? 'true' : 'false' }}]
                    @endforeach
                ]);


                var options = {
                    backgroundColor: {
                        fill: '#ffffff'
                    },
                    curveType: 'function',
                    legend: {position: 'bottom', alignment: 'end', textStyle: {color: 'black'}},
                    colors: ['#f9c80b', '#000000'],
                    areaOpacity: 0.27,
                    pointSize: 8,
                    lineWidth: 3,
                    chartArea: {left: 50, top: 10, right: 10, width: "100%", height: "70%"},
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
                    },
                    vAxis: {
                        gridlines: {
                            color: '#eee',
                            count: 6
                        },
                        viewWindow: {
                            max: 100,
                            min: 0
                        },
                        baselineColor: '#6a6f7a',
                        textStyle: {color: 'black'}
                    },
                    focusTarget: 'category'
                };

                var chart = new google.visualization.LineChart(document.getElementById('chart'));

                chart.draw(data, options);
            }
        </script>
    @endpush
@else
    <p>{{ __('A chart will be available after the first completed week') }}</p>
@endif
