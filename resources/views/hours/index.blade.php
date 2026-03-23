@extends('layouts.app')
@section('title', 'Stunden')
@section('content')
    <style>
        .hours-summary { margin: 10px 0 14px; }
        .hours-chart { height: 260px; }
        .hours-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
            font-size: 12px;
        }
        .hours-legend__item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .hours-legend__swatch {
            width: 18px;
            height: 10px;
            border: 1px solid rgba(0, 0, 0, 0.2);
            display: inline-block;
        }
        .hours-legend__swatch--bar { background: #4a7a2a; }
        .hours-legend__swatch--weekend { background: rgba(102, 0, 0, 0.65); }
        .hours-legend__swatch--vacation { background: rgba(0, 70, 0, 0.7); }
        .hours-legend__swatch--sickness { background: rgba(184, 134, 11, 0.7); }
        .hours-legend__swatch--year-average {
            height: 0;
            border: 0;
            border-top: 2px solid #b00020;
        }
        .hours-legend__swatch--week-average {
            height: 0;
            border: 0;
            border-top: 2px solid #1f5aa6;
        }
        .hours-legend__swatch--minimum {
            height: 0;
            border: 0;
            border-top: 2px dashed #666;
        }
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
                            <form method="GET" action="{{ route('hours.index') }}">
                                {{ html()->select('year', $years->all(), $selectedYear)->attribute('onchange', 'this.form.submit()') }}
                            </form>
                            <div class="hours-summary">
                                <div><strong>Gesamtstunden {{ $selectedYear }}:</strong> {{ $totalHours }}</div>
                                <div><strong>Dienstleistungstage (8h):</strong> {{ number_format($totalHours / 8, 2) }}</div>
                                <div><strong>Durchschnitt pro beruecksichtigtem Tag:</strong> {{ number_format($averageHours, 2) }}</div>
                                <div><strong>Forecast Dienstleistungstage bis Jahresende:</strong> {{ number_format($forecastServiceDays, 2) }}</div>
                            </div>
                            @if($dailyHours->isNotEmpty())
                                <div class="hours-chart">
                                    <canvas id="hoursChart" aria-label="Stunden pro Tag"></canvas>
                                </div>
                                <div class="hours-legend">
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--bar"></span>Stunden pro Tag</span>
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--weekend"></span>Wochenende</span>
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--vacation"></span>Urlaub</span>
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--sickness"></span>Krankheit</span>
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--year-average"></span>Jahresdurchschnitt</span>
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--week-average"></span>Wochendurchschnitt</span>
                                    <span class="hours-legend__item"><span class="hours-legend__swatch hours-legend__swatch--minimum"></span>8h Minimum</span>
                                </div>
                                <script src="/js/chart.min.js"></script>
                                <script>
                                    (function () {
                                        var labels = @json($dailyHours->keys()->values());
                                        var hoursData = @json($dailyHours->values()->values());
                                        var projectCompletionDates = @json($projectCompletionDates);
                                        var absenceChartData = @json($absenceChartData);
                                        var excludedAverageDates = @json($excludedAverageDates);
                                        var average = {{ number_format($averageHours, 2, '.', '') }};
                                        var minHours = 8;

                                        var avgLine = labels.map(function () { return average; });
                                        var minLine = labels.map(function () { return minHours; });
                                        var weeklyAverageMap = {};
                                        var weeklyAverageLine = [];

                                        function parseIsoDate(dateString) {
                                            var parts = dateString.split('-');
                                            return new Date(Date.UTC(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10)));
                                        }

                                        function getIsoWeekKey(dateString) {
                                            var date = parseIsoDate(dateString);
                                            var day = date.getUTCDay();
                                            if (day === 0) {
                                                day = 7;
                                            }

                                            date.setUTCDate(date.getUTCDate() + 4 - day);
                                            var isoYear = date.getUTCFullYear();
                                            var yearStart = new Date(Date.UTC(isoYear, 0, 1));
                                            var week = Math.ceil((((date - yearStart) / 86400000) + 1) / 7);

                                            return isoYear + '-W' + String(week).padStart(2, '0');
                                        }

                                        function hasWeekendProjectCompletion(dateString) {
                                            return projectCompletionDates.indexOf(dateString) !== -1;
                                        }

                                        function isExcludedAverageDate(dateString) {
                                            return excludedAverageDates.indexOf(dateString) !== -1;
                                        }

                                        labels.forEach(function (label, index) {
                                            var date = parseIsoDate(label);
                                            var day = date.getUTCDay();
                                            if (isExcludedAverageDate(label) || ((day === 0 || day === 6) && !hasWeekendProjectCompletion(label))) {
                                                return;
                                            }

                                            var weekKey = getIsoWeekKey(label);
                                            if (!weeklyAverageMap[weekKey]) {
                                                weeklyAverageMap[weekKey] = { total: 0, count: 0 };
                                            }

                                            weeklyAverageMap[weekKey].total += Number(hoursData[index] || 0);
                                            weeklyAverageMap[weekKey].count += 1;
                                        });

                                        labels.forEach(function (label) {
                                            var date = parseIsoDate(label);
                                            var day = date.getUTCDay();
                                            var weekData = weeklyAverageMap[getIsoWeekKey(label)];

                                            if (isExcludedAverageDate(label) || ((day === 0 || day === 6) && !hasWeekendProjectCompletion(label)) || !weekData || weekData.count === 0) {
                                                weeklyAverageLine.push(null);
                                                return;
                                            }

                                            weeklyAverageLine.push(Number((weekData.total / weekData.count).toFixed(2)));
                                        });

                                        var canvas = document.getElementById('hoursChart');
                                        if (!canvas) return;
                                        var ctx = canvas.getContext('2d');

                                        function getDayBounds(meta, chartArea, index) {
                                            var bar = meta.data[index];
                                            var currentX = bar._model.x;
                                            var previousX = index > 0 ? meta.data[index - 1]._model.x : null;
                                            var nextX = index < meta.data.length - 1 ? meta.data[index + 1]._model.x : null;

                                            return {
                                                left: previousX === null ? chartArea.left : (previousX + currentX) / 2,
                                                right: nextX === null ? chartArea.right : (currentX + nextX) / 2
                                            };
                                        }

                                        var backgroundPlugin = {
                                            beforeDatasetsDraw: function (chart) {
                                                var chartArea = chart.chartArea;
                                                var yScale = chart.scales['y-axis-0'];
                                                if (!chartArea || !yScale) return;

                                                var meta = chart.getDatasetMeta(0);
                                                if (!meta || !meta.data || !meta.data.length) return;

                                                var context = chart.ctx;
                                                context.save();

                                                meta.data.forEach(function (bar, index) {
                                                    var date = parseIsoDate(labels[index]);
                                                    var day = date.getUTCDay();
                                                    var bounds = getDayBounds(meta, chartArea, index);

                                                    if (day === 0 || day === 6) {
                                                        context.fillStyle = 'rgba(102, 0, 0, 0.65)';
                                                        context.fillRect(bounds.left, chartArea.top, bounds.right - bounds.left, chartArea.bottom - chartArea.top);
                                                    }

                                                    var absence = absenceChartData[labels[index]];
                                                    if (!absence || (absence.type !== 'urlaub' && absence.type !== 'krankheit')) {
                                                        return;
                                                    }

                                                    context.fillStyle = absence.type === 'urlaub'
                                                        ? 'rgba(0, 70, 0, 0.7)'
                                                        : 'rgba(184, 134, 11, 0.7)';
                                                    context.fillRect(
                                                        bounds.left,
                                                        yScale.getPixelForValue(absence.hours),
                                                        bounds.right - bounds.left,
                                                        chartArea.bottom - yScale.getPixelForValue(absence.hours)
                                                    );
                                                });

                                                context.restore();
                                            }
                                        };

                                        new Chart(ctx, {
                                            type: 'bar',
                                            plugins: [backgroundPlugin],
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
                                                        label: 'Wochendurchschnitt',
                                                        type: 'line',
                                                        data: weeklyAverageLine,
                                                        borderColor: '#1f5aa6',
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
                                                legend: { display: true, position: 'bottom' },
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
                                @if($project->customer->city)
                                    <a href="{{ route('customers.city', $project->customer->city->id) }}">
                                        <img src="/assets/flags/{{ $project->customer->city->country_code }}.png"> {{ $project->customer->city->name }}
                                    </a>
                                @else
                                    Kein Ort
                                @endif
                            </td>
                            @php
                                $endDate = \Carbon\Carbon::parse($project->end_date)->startOfDay();
                                $today = \Carbon\Carbon::today();
                                $daysRemaining = $today->diffInDays($endDate, false);
                                if ($daysRemaining > 0) {
                                    $endBg = 'green';
                                } elseif ($daysRemaining === 0) {
                                    $endBg = 'orange';
                                } else {
                                    $endBg = 'red';
                                }
                            @endphp
                            <td style="background-color: {{ $endBg }};">
                                {{ $endDate->toDateString() }} ({{ $daysRemaining }})
                            </td>
                            <td>{{ $project->hours ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
