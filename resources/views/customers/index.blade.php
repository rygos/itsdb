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
                @if(isset($city) && $city)
                    <tr>
                        <td colspan="6"><strong>Stadt: {{ $city->name }}</strong></td>
                    </tr>
                @endif
                @foreach($customers as $c)
                    @php
                        switch(@$c->projects()->orderBy('updated_at', 'DESC')->first()->status->name){
                            case 'NEW':
                                $color = 'none';
                                $textColor = 'inherit';
                                break;
                            case 'WIP':
                                $color = 'orange';
                                $textColor = 'black';
                                break;
                            case 'CHECK':
                                $color = 'blue';
                                $textColor = 'white';
                                break;
                            case 'WAIT FOR INFO':
                                $color = 'yellow';
                                $textColor = 'black';
                                break;
                            case 'ON HOLD':
                                $color = 'red';
                                $textColor = 'white';
                                break;
                            case 'FINISHED':
                                $color = 'green';
                                $textColor = 'white';
                                break;
                            default:
                                $color = 'none';
                                $textColor = 'inherit';
                        }
                    @endphp
                    <tr style="text-align: left;">
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">{{ $c->short_no }}</td>
                        <td style="background-color: {{ $color }};color: {{ $textColor }};"><a style="color: inherit;" href="{{ route('customers.view', $c->id) }}">{{ $c->sap_no }}</a></td>
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">{{ $c->name }}</td>
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">
                            <a style="color: inherit;" href="{{ route('customers.city', $c->city->id) }}">
                                <img src="/assets/flags/{{ $c->city->country_code }}.png"> {{ $c->city->name }}
                            </a>
                        </td>
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">{{ @$c->projects()->orderBy('updated_at','DESC')->first()->name }}</td>
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">{{ @$c->projects()->orderBy('updated_at', 'DESC')->first()->status->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
