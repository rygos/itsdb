@extends('layouts.app')
@section('title', 'Stunden')
@section('content')
    <style>
        .hours-summary { margin: 10px 0 14px; }
        .hours-chart { height: 260px; }
        .hours-legend { font-size: 12px; margin-top: 6px; }
    </style>
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th colspan="4">
                        <span id="title"><big>Stunden</big></span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">
                        @if($years->isNotEmpty())
                            {!! Form::open(['route' => 'hours.index', 'method' => 'get']) !!}
                                {!! Form::select('year', $years, $selectedYear, ['onchange' => 'this.form.submit()']) !!}
                            {!! Form::close() !!}
                            <div class="hours-summary">
                                <div><strong>Gesamtstunden {{ $selectedYear }}:</strong> {{ $totalHours }}</div>
                                <div><strong>Dienstleistungstage (8h):</strong> {{ number_format($totalHours / 8, 2) }}</div>
                                <div><strong>Durchschnitt pro Kalendertag:</strong> {{ number_format($averageHours, 2) }}</div>
                                <div><strong>Forecast Dienstleistungstage bis Jahresende:</strong> {{ number_format($forecastServiceDays, 2) }}</div>
                            </div>
                            @if($dailyHours->isNotEmpty())
                                <div class="hours-chart">
                                    <canvas id="hoursChart" aria-label="Stunden pro Tag"></canvas>
                                </div>
                                <div class="hours-legend">
                                    Balken: Stunden pro Tag | Rot: Durchschnitt | Grau: 8h Minimum
                                </div>
                                <script src="/js/chart.min.js"></script>
                                <script>
                                    (function () {
                                        var labels = @json($dailyHours->keys()->values());
                                        var hoursData = @json($dailyHours->values()->values());
                                        var average = {{ number_format($averageHours, 2, '.', '') }};
                                        var minHours = 8;

                                        var avgLine = labels.map(function () { return average; });
                                        var minLine = labels.map(function () { return minHours; });

                                        var canvas = document.getElementById('hoursChart');
                                        if (!canvas) return;
                                        var ctx = canvas.getContext('2d');

                                        new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: labels,
                                                datasets: [
                                                    {
                                                        label: 'Stunden',
                                                        data: hoursData,
                                                        backgroundColor: '#4a7a2a',
                                                        borderWidth: 0
                                                    },
                                                    {
                                                        label: 'Durchschnitt',
                                                        type: 'line',
                                                        data: avgLine,
                                                        borderColor: '#b00020',
                                                        borderWidth: 2,
                                                        pointRadius: 0,
                                                        lineTension: 0,
                                                        fill: false
                                                    },
                                                    {
                                                        label: '8h Minimum',
                                                        type: 'line',
                                                        data: minLine,
                                                        borderColor: '#666',
                                                        borderWidth: 2,
                                                        pointRadius: 0,
                                                        borderDash: [6, 4],
                                                        lineTension: 0,
                                                        fill: false
                                                    }
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                scales: {
                                                    xAxes: [{
                                                        ticks: { maxRotation: 90, minRotation: 90 }
                                                    }],
                                                    yAxes: [{
                                                        ticks: { beginAtZero: true },
                                                        scaleLabel: { display: true, labelString: 'Stunden' }
                                                    }]
                                                },
                                                legend: { display: false },
                                                tooltips: {
                                                    callbacks: {
                                                        label: function (tooltipItem, data) {
                                                            var dataset = data.datasets[tooltipItem.datasetIndex];
                                                            if (dataset.type === 'line') {
                                                                return dataset.label + ': ' + tooltipItem.yLabel + 'h';
                                                            }
                                                            return 'Stunden: ' + tooltipItem.yLabel + 'h';
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    })();
                                </script>
                            @endif
                        @else
                            <em>Keine abgeschlossenen Projekte vorhanden.</em>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        @if($projects->isNotEmpty())
            <table id="pouetbox_prodmain">
                <thead>
                    <tr id="prodheader">
                        <th>Short</th>
                        <th>SAP</th>
                        <th>Project</th>
                        <th>Customer</th>
                        <th>City</th>
                        <th>Finished</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentWeek = null;
                        $weekIndex = 0;
                    @endphp
                    @foreach($projects as $project)
                        @php
                            $week = \Carbon\Carbon::parse($project->end_date)->isoWeek();
                            $showWeekHeader = false;
                            if ($currentWeek !== $week) {
                                $currentWeek = $week;
                                $weekIndex++;
                                $showWeekHeader = true;
                            }
                            $rowBg = ($weekIndex % 2 === 0) ? '#f2f2f2' : '#ffffff';
                        @endphp
                        @if($showWeekHeader)
                            <tr style="background-color: {{ $rowBg }};">
                                <td colspan="7"><strong>Kalenderwoche {{ $currentWeek }}</strong></td>
                            </tr>
                        @endif
                        <tr style="text-align: left;background-color: {{ $rowBg }};">
                            <td>{{ $project->customer->short_no }}</td>
                            <td><a href="{{ route('projects.view', $project->id) }}">{{ $project->customer->sap_no }}</a></td>
                            <td><a href="{{ route('projects.view', $project->id) }}">{{ $project->name }}</a></td>
                            <td><a href="{{ route('customers.view', $project->customer_id) }}">{{ $project->customer->name }}</a></td>
                            <td>
                                <a href="{{ route('customers.city', $project->customer->city->id) }}">
                                    <img src="assets/flags/{{ $project->customer->city->country_code }}.png"> {{ $project->customer->city->name }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($project->end_date)->toDateString() }}</td>
                            <td>{{ $project->hours ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
