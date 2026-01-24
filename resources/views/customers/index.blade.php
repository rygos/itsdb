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
                        $latestProject = $c->latestProject;
                        $statusName = $latestProject ? optional($latestProject->status)->name : null;
                        $color = \App\Helpers\StatusHelper::color($statusName);
                        $textColor = \App\Helpers\StatusHelper::textColor($statusName);
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
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">{{ $latestProject ? $latestProject->name : '-' }}</td>
                        <td style="background-color: {{ $color }};color: {{ $textColor }};">{{ $statusName ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
