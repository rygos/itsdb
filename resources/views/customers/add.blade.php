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
                    {{ html()->form()->route('customers.store')->open() }}
                    <table id="stattable">
                        <tr>
                            <td>Short No.:</td>
                            <td>{{ html()->text('short_no') }}</td>
                        </tr>
                        <tr>
                            <td>SAP No.:</td>
                            <td>{{ html()->text('sap_no') }}</td>
                        </tr>
                        <tr>
                            <td>Dynamics No.:</td>
                            <td>{{ html()->text('dynamics_no') }}</td>
                        </tr>
                        <tr>
                            <td>Customer Name:</td>
                            <td>{{ html()->text('name') }}</td>
                        </tr>
                        <tr>
                            <td>Citys:</td>
                            <td>{{ html()->select('city', $citys) }}</td>
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
                    {{ html()->form()->route('city.add')->open() }}
                    <table id="stattable">
                        <tr>
                            <td>City Name:</td>
                            <td>{{ html()->text('name') }}</td>
                        </tr>
                        <tr>
                            <td>SAP No.:</td>
                            <td>{{ html()->select('country_code', $countrys) }}</td>
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
