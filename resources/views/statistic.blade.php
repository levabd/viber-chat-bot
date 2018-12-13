@extends('layouts.app')

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <h1>На этот месяц</h1>
                <canvas id="monthStatisticChart"></canvas>
@foreach($month_statistic as $key => $value)
<tr>
<td>{{ $key }}</td>
<td>{{ $value }}</td>
</tr>
@endforeach
</div>
</div>

<div class="row justify-content-center">
        <div class="col-md-8">
                <h1>На следующий месяц</h1>
                <canvas id="nextMonthStatisticChart"></canvas>
@foreach($next_month_statistic as $key => $value)
<tr>
<td>{{ $key }}</td>
<td>{{ $value }}</td>
</tr>
@endforeach
</div>
        </div>
        <div class="row justify-content-center">
        <div class="col-md-8">
                <canvas id="drugStatistic"></canvas>
                </div>
                </div>
</div>
</div>
<script>
try {
var monthStatisticCtx = document.getElementById('monthStatisticChart').getContext('2d');
var monthStatisticChart = new Chart(monthStatisticCtx, {
    // The type of chart we want to create
    type: 'line',
    // The data for our dataset
    data: {
        labels: ["{{ implode('", "', array_keys($month_statistic)) }}"],
        datasets: [{
            label: "Month statistic chart",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [{{ implode(', ', array_values($month_statistic)) }}],
        }]
    },
//Configuration options go here
    options: {}
});

var nextMonthStatisticCtx = document.getElementById('nextMonthStatisticChart').getContext('2d');
var nextMonthStatisticChart = new Chart(nextMonthStatisticCtx, {
    // The type of chart we want to create
    type: 'line',
    // The data for our dataset
    data: {
        labels: ["{{ implode('", "', array_keys($next_month_statistic)) }}"],
        datasets: [{
            label: "Next month statistic chart",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [{{ implode(', ', array_values($month_statistic)) }}],
        }]
    },
//Configuration options go here
    options: {}
});
} catch(err) {
consol.log(err);	
}
</script>
@endsection
