@extends('layouts.app')

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>На текущий месяц</h1>
            <canvas id="monthStatisticChart"></canvas>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>На следующий месяц</h1>
            <canvas id="nextMonthStatisticChart"></canvas>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>Статистика использования препаратов</h1>
            <div id="canvas-holder" style="width:50%; margin: 0 auto;">
                <canvas id="drugStatistic"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script>
    var monthStatisticCtx = document.getElementById('monthStatisticChart').getContext('2d');
    var monthStatisticChart = new Chart(monthStatisticCtx, {
        type: 'bar',
        data: {
            labels: [{{ implode(', ', array_keys($month_statistic)) }}],
            datasets: [{
                label: 'На текущий месяц',
                data: [{{ implode(', ', array_values($month_statistic)) }}],
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });

    var nextMonthStatisticCtx = document.getElementById('nextMonthStatisticChart').getContext('2d');
    var nextMonthStatisticChart = new Chart(nextMonthStatisticCtx, {
        type: 'bar',
        data: {
            labels: [{{ implode(', ', array_keys($next_month_statistic)) }}],
            datasets: [{
                label: 'На следующий месяц',
                data: [{{ implode(', ', array_values($next_month_statistic)) }}],
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
    
    var drugStatisticCtx = document.getElementById('drugStatistic').getContext('2d');
    var drugStatisticChart = new Chart(drugStatisticCtx, {
        type: 'pie',
        data: {
            labels: ['{{ array_keys($total_drug_statistic)[0] }}', '{{ array_keys($total_drug_statistic)[1] }}'],
            datasets: [{
                data: ['{{ array_values($total_drug_statistic)[0] }}', '{{ array_values($total_drug_statistic)[1] }}'],
                backgroundColor: [
                    'rgb(75, 192, 192)',
					'rgb(54, 162, 235)',
                ]
            }]
        },
        options: { }
    });
    // total_drug_statistic
</script>
@endsection