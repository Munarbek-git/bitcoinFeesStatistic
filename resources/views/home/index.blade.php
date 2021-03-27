<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>Статистика по комиссии</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        let array_data = JSON.parse('{!! $chart_data !!}');

        function drawChart() {
            var data = google.visualization.arrayToDataTable(array_data);

            var options = {
                title: 'Company Performance',
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <h1>Последние данные по комиссиям за последние 100 блока</h1>
        <div class="col" style="border: 1px solid black">
            <div>Средняя цена комиссии</div>
            <div>{{  ceil($last_block->avg) }}</div>
        </div>
        <div class="col" style="border: 1px solid black">
            <div>Минимальная цена комиссии</div>
            <div>{{  $last_block->min }}</div>
        </div>
        <div class="col" style="border: 1px solid black">
            <div>Максимальная цена комиссии</div>
            <div>{{  $last_block->max }}</div>
        </div>
    </div>
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
</div>
</body>
