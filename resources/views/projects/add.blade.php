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
                    {{ html()->form()->route('projects.store')->open() }}
                    <table id="stattable">
                        <tr>
                            <td>Dynamics ID:</td>
                            <td>{{ html()->text('dynamics_id') }}</td>
                        </tr>
                        <tr>
                            <td>Project Name:</td>
                            <td>{{ html()->text('name') }}</td>
                        </tr>
                        <tr>
                            <td>City:</td>
                            <td>{{ html()->select('customer', $customers) }}</td>
                        </tr>
                        <tr>
                            <td>Start Date:</td>
                            <td>{{ html()->input('date', 'start_date', now()->toDateString()) }}</td>
                        </tr>
                        <tr>
                            <td>End Date:</td>
                            <td>{{ html()->input('date', 'end_date', now()->toDateString()) }}</td>
                        </tr>
                        <tr>
                            <td>Hours:</td>
                            <td>{{ html()->input('number', 'hours')->attribute('min', 0) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>{{ html()->submit('Submit') }}</td>
                        </tr>

                    </table>
                    {{ html()->form()->close() }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
