@php($rand = rand(100000, 9999999999))
<div id="{{ $rand }}"></div>

@push('scripts')
    <script>
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(f{{ $rand }});


        let timeout{{ $rand  }};
        window.onresize = function () {
            clearTimeout(timeout{{ $rand }});
            timeout{{ $rand }} = setTimeout(() => {
                f{{ $rand }}();
            }, 100);
        }

        function f{{ $rand }}() {

            var data = google.visualization.arrayToDataTable([
                ['{{ __("Type") }}', '{{ __("Score") }}'],
                ['{{ __("Self") }} ({{ $answersSelf->count() }})', {{ $answersSelf->average('answer_number') ?? 0 }}],
                ['{{ __("Others") }} ({{ $answersOthers->count() }})', {{ $answersOthers->average('answer_number') ?? 0 }}]
            ]);

            var options = {
                colors: ['#f9c80b', '#000000'],
                chartArea: {width: '75%'},
                hAxis: {
                    minValue: -3,
                    maxValue: 3,
                    gridlines: {count: 7}
                },
                legend: {position: "none"}
            };

            (new google.visualization.BarChart(document.getElementById('{{ $rand }}'))).draw(data, options);
        }
    </script>
@endpush
