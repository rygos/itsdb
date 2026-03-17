@extends('layouts.app')
@section('title', 'Customer bearbeiten')
@section('content')
    <div id="prodpagecontainer">
        {{ Form::open(['route' => ['customers.update', $customer]]) }}
        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="2">
                    <span id="title"><big>Customer bearbeiten</big></span>
                </th>
            </tr>
            </thead>
            <tbody>
            @if($errors->any())
                <tr>
                    <td colspan="2">{{ $errors->first() }}</td>
                </tr>
            @endif
            <tr>
                <td>Short No.</td>
                <td>{!! Form::text('short_no', old('short_no', $customer->short_no)) !!}</td>
            </tr>
            <tr>
                <td>SAP No.</td>
                <td>{!! Form::text('sap_no', old('sap_no', $customer->sap_no)) !!}</td>
            </tr>
            <tr>
                <td>Customer Name</td>
                <td>{!! Form::text('name', old('name', $customer->name)) !!}</td>
            </tr>
            <tr>
                <td>Ort</td>
                <td>{!! Form::select('city', $citys, old('city', $customer->city_id)) !!}</td>
            </tr>
            <tr>
                <td></td>
                <td>
                    {{ Form::submit('Speichern') }}
                    <a href="{{ route('customers.view', $customer) }}">Zurueck</a>
                </td>
            </tr>
            </tbody>
        </table>
        {{ Form::close() }}
    </div>
@endsection
