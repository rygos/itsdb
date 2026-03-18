@extends('layouts.app')
@section('title', 'Customer bearbeiten')
@section('content')
    <div id="prodpagecontainer">
        {{ html()->modelForm($customer)->route('customers.update', $customer)->open() }}
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
                <td>{{ html()->text('short_no', old('short_no', $customer->short_no)) }}</td>
            </tr>
            <tr>
                <td>SAP No.</td>
                <td>{{ html()->text('sap_no', old('sap_no', $customer->sap_no)) }}</td>
            </tr>
            <tr>
                <td>Customer Name</td>
                <td>{{ html()->text('name', old('name', $customer->name)) }}</td>
            </tr>
            <tr>
                <td>Ort</td>
                <td>{{ html()->select('city', $citys, old('city', $customer->city_id)) }}</td>
            </tr>
            <tr>
                <td></td>
                <td>
                    {{ html()->submit('Speichern') }}
                    <a href="{{ route('customers.view', $customer) }}">Zurueck</a>
                </td>
            </tr>
            </tbody>
        </table>
        {{ html()->closeModelForm() }}
    </div>
@endsection
