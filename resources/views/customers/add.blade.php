@extends('layouts.app')
@section('title', 'Add Customer')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <span id='title'><big>Add Customer</big></span>
                    <div id='nfo'></div>
                </th>
            </tr>
            <tr>
                <td>
                    {!! Form::open(['route' => 'customers.store']) !!}
                    <table id="stattable">
                        <tr>
                            <td>Short No.:</td>
                            <td>{!! Form::text('short_no') !!}</td>
                        </tr>
                        <tr>
                            <td>SAP No.:</td>
                            <td>{!! Form::text('sap_no') !!}</td>
                        </tr>
                        <tr>
                            <td>Dynamics No.:</td>
                            <td>{!! Form::text('dynamics_no') !!}</td>
                        </tr>
                        <tr>
                            <td>Customer Name:</td>
                            <td>{!! Form::text('name') !!}</td>
                        </tr>
                        <tr>
                            <td>Citys:</td>
                            <td>{!! Form::select('city', $citys) !!}</td>
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
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <span id='title'><big>Add City</big></span>
                    <div id='nfo'></div>
                </th>
            </tr>
            <tr>
                <td>
                    {!! Form::open(['route' => 'customers.store']) !!}
                    <table id="stattable">
                        <tr>
                            <td>City Name:</td>
                            <td>{!! Form::text('name') !!}</td>
                        </tr>
                        <tr>
                            <td>SAP No.:</td>
                            <td>{!! Form::select('country_code', $countrys) !!}</td>
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
