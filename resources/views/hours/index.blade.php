@extends('layouts.app')
@section('title', 'Stunden')
@section('content')
    <style>
        .hours-summary { margin: 10px 0 14px; }
        .hours-chart { position: relative; height: 160px; border: 1px solid #ccc; padding: 8px; overflow-x: auto; }
        .hours-chart-inner { display: flex; align-items: flex-end; gap: 2px; height: 100%; }
        .hours-bar { width: 6px; background: #4a7a2a; }
        .hours-line { position: absolute; left: 0; right: 0; border-top: 1px dashed; }
        .hours-line-average { border-color: #b00020; }
        .hours-line-min { border-color: #666; }
        .hours-line-label { position: absolute; right: 8px; top: -10px; font-size: 10px; background: #fff; padding: 0 4px; }
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
                                @php
                                    $chartHeight = 160;
                                @endphp
                                <div class="hours-chart" aria-label="Stunden pro Tag">
                                    @php
                                        $averageLineOffset = $chartHeight - (($averageHours / $maxDailyHours) * $chartHeight);
                                        $minLineOffset = $chartHeight - ((8 / $maxDailyHours) * $chartHeight);
                                    @endphp
                                    <div class="hours-line hours-line-average" style="top: {{ max(0, min($chartHeight, $averageLineOffset)) }}px;">
                                        <span class="hours-line-label">Durchschnitt</span>
                                    </div>
                                    <div class="hours-line hours-line-min" style="top: {{ max(0, min($chartHeight, $minLineOffset)) }}px;">
                                        <span class="hours-line-label">8h</span>
                                    </div>
                                    <div class="hours-chart-inner">
                                        @foreach($dailyHours as $date => $hours)
                                            @php
                                                $barHeight = ($hours / $maxDailyHours) * $chartHeight;
                                            @endphp
                                            <div class="hours-bar" style="height: {{ max(1, $barHeight) }}px;" title="{{ $date }}: {{ $hours }}h"></div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="hours-legend">
                                    Balken: Stunden pro Tag | Rot: Durchschnitt | Grau: 8h Minimum
                                </div>
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
