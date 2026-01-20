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
                                <strong>Gesamtstunden {{ $selectedYear }}:</strong> {{ $totalHours }}
                                <span> | <strong>Durchschnitt pro Tag (mit Eintrag):</strong> {{ number_format($averageHours, 2) }}</span>
                            </div>
                            @if($dailyHours->isNotEmpty())
                                <div class="hours-chart">
                                    <canvas id="hoursChart" aria-label="Stunden pro Tag"></canvas>
                                </div>
                                <div class="hours-legend">
                                    Balken: Stunden pro Tag | Rot: Durchschnitt | Grau: 8h Minimum
                                </div>
                                <script src="/js/chart.umd.min.js"></script>
                                <script>
                                    (function () {
                                        var labels = @json($dailyHours->keys()->values());
                                        var hoursData = @json($dailyHours->values()->values());
                                        var average = {{ number_format($averageHours, 2, '.', '') }};
                                        var minHours = 8;

                                        var avgLine = labels.map(function () { return average; });
                                        var minLine = labels.map(function () { return minHours; });

                                        var ctx = document.getElementById('hoursChart');
                                        if (!ctx) return;

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
                                                        tension: 0
                                                    },
                                                    {
                                                        label: '8h Minimum',
                                                        type: 'line',
                                                        data: minLine,
                                                        borderColor: '#666',
                                                        borderWidth: 2,
                                                        pointRadius: 0,
                                                        borderDash: [6, 4],
                                                        tension: 0
                                                    }
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: { display: false },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function (context) {
                                                                if (context.dataset.type === 'line') {
                                                                    return context.dataset.label + ': ' + context.parsed.y + 'h';
                                                                }
                                                                return 'Stunden: ' + context.parsed.y + 'h';
                                                            }
                                                        }
                                                    }
                                                },
                                                scales: {
                                                    x: {
                                                        ticks: { maxRotation: 90, minRotation: 90 }
                                                    },
                                                    y: {
                                                        beginAtZero: true,
                                                        title: { display: true, text: 'Stunden' }
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
                    @foreach($projects as $project)
                        <tr style="text-align: left;">
                            <td>{{ $project->customer->short_no }}</td>
                            <td><a href="{{ route('projects.view', $project->id) }}">{{ $project->customer->sap_no }}</a></td>
                            <td><a href="{{ route('projects.view', $project->id) }}">{{ $project->name }}</a></td>
                            <td><a href="{{ route('customers.view', $project->customer_id) }}">{{ $project->customer->name }}</a></td>
                            <td><img src="assets/flags/{{ $project->customer->city->country_code }}.png"> {{ $project->customer->city->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($project->end_date)->toDateString() }}</td>
                            <td>{{ $project->hours ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
