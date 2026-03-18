@extends('layouts.app')
@section('title', 'Logs')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th>User</th>
                    <th>Short</th>
                    <th>SAP</th>
                    <th>Kunde</th>
                    <th>Type</th>
                    <th>Log</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $item)
                    @php($customer = $item->customer)
                    <tr style="text-align: left;">
                        <td>{{ $item->user?->name ?? '-' }}</td>
                        <td>{{ $customer?->short_no ?? '-' }}</td>
                        <td>
                            @if($customer)
                                <a href="{{ route('customers.view', $customer) }}">{{ $customer->sap_no }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $customer?->name ?? '-' }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->msg }}</td>
                        <td>{{ optional($item->created_at)->format('d.m.Y H:i:s') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Keine Logs vorhanden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
