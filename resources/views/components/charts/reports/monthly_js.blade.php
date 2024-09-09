@push('scripts')
    <script>
        google.charts.load('current', {'packages': ['corechart', 'line']});
        google.charts.setOnLoadCallback(drawAll);

        var options = {
            backgroundColor: {
                fill: 'transparent'
            },
            curveType: 'function',
            legend: {position: 'bottom', alignment: 'end', textStyle: {color: 'black'}},
            animation: {
                startup: true
            },
            colors: ['#000000', '#f9c80b', '#0d69f9', '#BBBBBB'],
            areaOpacity: 0.07,
            pointSize: 8,
            lineWidth: 3,
            chartArea: {left: 50, top: 10, right: 10, width: "100%", height: "70%"},
            hAxis: {
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
                baselineColor: '#6a6f7a',
                textStyle: {color: 'black'}
            },
            animation: {
                duration: 1000,
                startup: false,
                easing: 'inAndOut'
            },
            series: {
                0: {pointShape: {type: 'circle'}},
                1: {pointShape: {type: 'circle'}, pointSize: 0},
                2: {pointShape: {type: 'circle'}, pointSize: 0},
                3: {pointShape: {type: 'circle'}, pointSize: 0},
            },
            focusTarget: 'category'
        };

        let timeout;
        window.onresize = function () {
            clearTimeout(timeout);
            timeout = setTimeout(drawAll, 100);
        }

        function drawAll() {
            @foreach ($data->get('matrix') as $matri)
            draw{{ $matri['question_id'] }}();
            @endforeach
        }
    </script>
@endpush
