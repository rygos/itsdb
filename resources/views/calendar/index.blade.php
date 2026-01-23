@extends('layouts.app')
@section('title', 'Calendar')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th>
                    <span id='title'><big>Calendar - {{ $date->monthName }} {{ $date->yearIso }}</big></span>
                    <div id='nfo'></div>
                </th>
            </tr>
            <tr>
                <td>
                    <div style="margin-bottom: 10px;">
                        <label for="calendar-year">Year:</label>
                        <select id="calendar-year" onchange="window.location='{{ url('/calendar') }}/'+this.value+'/{{ $date->format('m') }}'">
                            @foreach($years as $year)
                                <option value="{{ $year }}" @if($year == $date->format('Y')) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                        @php
                            $prev = $date->copy()->subMonth();
                            $next = $date->copy()->addMonth();
                        @endphp
                        <a href="{{ route('calendar.index', ['year' => $prev->format('Y'), 'month' => $prev->format('m')]) }}">&laquo;</a>
                        <strong style="padding: 0 6px;">{{ $date->format('F') }}</strong>
                        <a href="{{ route('calendar.index', ['year' => $next->format('Y'), 'month' => $next->format('m')]) }}">&raquo;</a>
                    </div>
                    <div class="calendar">
                        <div class="month-year">
                            <span class="month">{{ $date->format('M') }}</span>
                            <span class="year">{{ $date->format('Y') }}</span>
                        </div>
                        <div class="days">
                            @foreach($day_labels as $dl)
                                <span class="day-label">{{ $dl }}</span>
                            @endforeach

                            @php
                                $cursor = $start_of_calendar->copy();
                            @endphp
                            @while($cursor <= $end_of_calendar)
                                @php
                                    $extra_class = $cursor->format('m') != $date->format('m') ? 'dull' : '';
                                    $extra_class .= $cursor->isToday() ? ' today' : '';
                                    $dayKey = $cursor->toDateString();
                                    $count = $projectCounts[$dayKey] ?? 0;
                                    $isSelected = $selectedDay === $dayKey ? ' selected' : '';
                                @endphp
                                <span class="day {{ $extra_class }}{{ $isSelected }}">
                                    <span class="content">
                                        <a href="{{ route('calendar.index', ['year' => $date->format('Y'), 'month' => $date->format('m')]) }}?day={{ $dayKey }}">
                                            {{ $cursor->format('j') }}
                                        </a>
                                        <span style="display: block; font-size: 11px;">({{ $count }})</span>
                                    </span>
                                </span>
                                @php
                                    $cursor->addDay()
                                @endphp
                            @endwhile
                        </div>
                    </div>
                    @if($selectedDay)
                        <div style="margin-top: 12px;">
                            <strong>Projekte mit Enddatum {{ $selectedDay }}</strong>
                        </div>
                        @if($selectedProjects->isNotEmpty())
                            <table id="pouetbox_prodmain" style="margin-top: 6px;">
                                <thead>
                                    <tr id="prodheader">
                                        <th>Short</th>
                                        <th>SAP</th>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>City</th>
                                        <th>End Date</th>
                                        <th>Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedProjects as $project)
                                        <tr style="text-align: left;">
                                            <td>{{ $project->customer->short_no }}</td>
                                            <td><a href="{{ route('projects.view', $project->id) }}">{{ $project->customer->sap_no }}</a></td>
                                            <td><a href="{{ route('projects.view', $project->id) }}">{{ $project->name }}</a></td>
                                            <td><a href="{{ route('customers.view', $project->customer_id) }}">{{ $project->customer->name }}</a></td>
                                            <td>
                                                <a href="{{ route('customers.city', $project->customer->city->id) }}">
                                                    <img src="/assets/flags/{{ $project->customer->city->country_code }}.png"> {{ $project->customer->city->name }}
                                                </a>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->toDateString() }}</td>
                                            <td>{{ $project->hours ?? '-' }}</td>
                                            <td>{{ $project->status->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div style="margin-top: 6px;"><em>Keine Projekte gefunden.</em></div>
                        @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>
@endsection
