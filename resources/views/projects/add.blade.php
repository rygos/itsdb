@extends('layouts.app')
@section('title', 'Add Project')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <span id='title'><big>Add Project</big></span>
                    <div id='nfo'></div>
                </th>
            </tr>
            <tr>
                <td>
                    {!! Form::open(['route' => 'projects.store']) !!}
                    <table id="stattable">
                        <tr>
                            <td>Dynamics ID:</td>
                            <td>{!! Form::text('dynamics_id') !!}</td>
                        </tr>
                        <tr>
                            <td>Project Name:</td>
                            <td>{!! Form::text('name') !!}</td>
                        </tr>
                        <tr>
                            <td>City:</td>
                            <td>{!! Form::select('customer', $customers) !!}</td>
                        </tr>
                        <tr>
                            <td>Start Date:</td>
                            <td>{!! Form::date('start_date', now()->toDateString()) !!}</td>
                        </tr>
                        <tr>
                            <td>End Date:</td>
                            <td>{!! Form::date('end_date', now()->toDateString()) !!}</td>
                        </tr>
                        <tr>
                            <td>Hours:</td>
                            <td>{!! Form::number('hours', null, ['min' => 0]) !!}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>{{ Form::submit('Submit') }}</td>
                        </tr>

                    </table>
                    {!! Form::close() !!}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
