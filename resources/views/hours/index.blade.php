@extends('layouts.app')
@section('title', 'Stunden')
@section('content')
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
