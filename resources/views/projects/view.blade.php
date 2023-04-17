@extends('layouts.app')
@section('title', 'Projectinfo')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th>
                    <span id="title"><big>{{ $project->name }}</big></span>
                    <div id="nfo"><a href="{{ route('customers.view', $project->customer_id) }}">{{ $project->customer->short_no }} - {{ $project->customer->sap_no }} - {{ $project->customer->name }}</a></div>
                </th>
            </tr>
            <tr>
                <td>
                    Created by: {{ $project->user->name }}
                </td>
            </tr>
        </table>
    </div>
@endsection
