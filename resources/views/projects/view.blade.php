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
            {{ Form::open(['route' => 'projects.update']) }}
            {{ Form::hidden('id', $project->id) }}
            <tr>
                <td>Created by:</td>
                <td>{{ $project->user->name }}</td>
            </tr>
            <tr>
                <td>Name:</td>
                <td>{!! Form::text('name', $project->name) !!}</td>
            </tr>
            <tr>
                <td>Dynamics ID:</td>
                <td>{!! Form::text('dynamics_id', $project->dynamics_id) !!}</td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{!! Form::date('start_date', \Carbon\Carbon::parse($project->start_date)->toDateString()) !!} {!! Form::time('start_date_time', \Carbon\Carbon::parse($project->start_date)->format('H:i')) !!}</td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>{!! Form::date('end_date', \Carbon\Carbon::parse($project->end_date)->toDateString()) !!}  {!! Form::time('end_date_time', \Carbon\Carbon::parse($project->end_date)->format('H:i')) !!}</td>
            </tr>
            <tr>
                <td colspan="2">{!! Form::submit('Submit') !!}</td>
            </tr>
            {{ Form::close() }}
        </table>
    </div>
@endsection
