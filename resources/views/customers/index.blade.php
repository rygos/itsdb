@extends('layouts.app')
@section('title', 'Customers')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th>Short</th>
                    <th>SAP</th>
                    <th>Customer</th>
                    <th>City</th>
                    <th>Last Project</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $c)
                    @php
                        switch(@$c->projects()->orderBy('updated_at', 'DESC')->first()->status->name){
                            case 'NEW':
                                $color = 'none';
                                break;
                            case 'WIP':
                                $color = 'orange';
                                break;
                            case 'CHECK':
                                $color = 'blue';
                                break;
                            case 'WAIT FOR INFO':
                                $color = 'yellow';
                                break;
                            case 'ON HOLD':
                                $color = 'red';
                                break;
                            case 'FINISHED':
                                $color = 'green';
                                break;
                            default:
                                $color = 'none';
                        }
                    @endphp
                    <tr style="text-align: left;">
                        <td style="background-color: {{ $color }};">{{ $c->short_no }}</td>
                        <td style="background-color: {{ $color }};"><a href="{{ route('customers.view', $c->id) }}">{{ $c->sap_no }}</a></td>
                        <td style="background-color: {{ $color }};">{{ $c->name }}</td>
                        <td style="background-color: {{ $color }};">
                            <a href="{{ route('customers.city', $c->city->id) }}">
                                <img src="assets/flags/{{ $c->city->country_code }}.png"> {{ $c->city->name }}
                            </a>
                        </td>
                        <td style="background-color: {{ $color }};">{{ @$c->projects()->orderBy('updated_at','DESC')->first()->name }}</td>
                        <td style="background-color: {{ $color }};">{{ @$c->projects()->orderBy('updated_at', 'DESC')->first()->status->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
