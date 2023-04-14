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
                    <div class="calendar">
                        <div class="month-year">
                            <span class="month">{{ $date->format('M') }}</span>
                            <span class="year">{{ $date->format('Y') }}</span>
                        </div>
                        <div class="days">
                            @foreach($day_labels as $dl)
                                <span class="day-label">{{ $dl }}</span>
                            @endforeach

                            @while($start_of_calendar <= $end_of_calendar)
                                @php
                                    $extra_class = $start_of_calendar->format('m') != $date->format('m') ? 'dull' : '';
                                    $extra_class .= $start_of_calendar->isToday() ? ' today' : '';
                                @endphp
                                <span class="day {{ $extra_class }}">
                                    <span class="content">
                                        {{ $start_of_calendar->format('j') }}
                                    </span>
                                </span>
                                @php
                                    $start_of_calendar->addDay()
                                @endphp
                            @endwhile
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
