<div id="chartQ{{ $matri['question_id'] }}" style="height: 250px; min-width: 600px" class="w-full"></div>

@push('scripts')
    <script type="text/javascript">
        function draw{{ $matri['question_id'] }} () {
            setTimeout(() => {
                var data = google.visualization.arrayToDataTable({!! json_encode($matri['graphData']) !!});
                var chart = new google.visualization.LineChart(document.getElementById('chartQ{{ $matri['question_id'] }}'));

                chart.draw(data, options);
            })
        }
    </script>
@endpush
