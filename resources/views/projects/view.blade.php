@extends('layouts.app')
@section('title', 'Projectinfo')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th colspan="2">
                    <span id="title"><big>{{ $project->name }}</big></span>
                    <div id="nfo"><a href="{{ route('customers.view', $project->customer_id) }}">{{ $project->customer->short_no }} - {{ $project->customer->sap_no }} - {{ $project->customer->name }}</a></div>
                </th>
            </tr>
            <tr>
                <td>Created by:</td>
                <td>{{ $project->user->name }}</td>
            </tr>
            @if($canManageProject)
                {{ html()->modelForm($project)->route('projects.update')->open() }}
                {{ html()->hidden('id', $project->id) }}
                <tr>
                    <td>Name:</td>
                    <td>{{ html()->text('name', $project->name) }}</td>
                </tr>
                <tr>
                    <td>Dynamics ID:</td>
                    <td>{{ html()->text('dynamics_id', $project->dynamics_id) }}</td>
                </tr>
                <tr>
                    <td>Start Date:</td>
                    <td>{{ html()->input('date', 'start_date', \Carbon\Carbon::parse($project->start_date)->toDateString()) }}</td>
                </tr>
                <tr>
                    <td>End Date:</td>
                    <td>{{ html()->input('date', 'end_date', \Carbon\Carbon::parse($project->end_date)->toDateString()) }}</td>
                </tr>
                <tr>
                    <td>Hours:</td>
                    <td>{{ html()->input('number', 'hours', $project->hours)->attribute('min', 0) }}</td>
                </tr>
                <tr>
                    <td colspan="2">{{ html()->submit('Submit') }}</td>
                </tr>
                {{ html()->closeModelForm() }}
            @else
                <tr>
                    <td>Name:</td>
                    <td>{{ $project->name }}</td>
                </tr>
                <tr>
                    <td>Dynamics ID:</td>
                    <td>{{ $project->dynamics_id }}</td>
                </tr>
                <tr>
                    <td>Start Date:</td>
                    <td>{{ \Carbon\Carbon::parse($project->start_date)->toDateString() }}</td>
                </tr>
                <tr>
                    <td>End Date:</td>
                    <td>{{ \Carbon\Carbon::parse($project->end_date)->toDateString() }}</td>
                </tr>
                <tr>
                    <td>Hours:</td>
                    <td>{{ $project->hours ?? '-' }}</td>
                </tr>
            @endif
        </table>
    </div>
@endsection
