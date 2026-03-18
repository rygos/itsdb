@extends('layouts.app')
@section('title', 'Logs')
@section('content')
    <div id="prodpagecontainer">
        @php
            $windowStart = max(1, $logs->currentPage() - 2);
            $windowEnd = min($logs->lastPage(), $logs->currentPage() + 2);
        @endphp

        @if($logs->hasPages())
            <div class="logs-pagination">
                @if($logs->onFirstPage())
                    <span class="logs-pagination__item is-disabled">Zurueck</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="logs-pagination__item">Zurueck</a>
                @endif

                @for($page = $windowStart; $page <= $windowEnd; $page++)
                    @if($page === $logs->currentPage())
                        <span class="logs-pagination__item is-active">{{ $page }}</span>
                    @else
                        <a href="{{ $logs->url($page) }}" class="logs-pagination__item">{{ $page }}</a>
                    @endif
                @endfor

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="logs-pagination__item">Weiter</a>
                @else
                    <span class="logs-pagination__item is-disabled">Weiter</span>
                @endif

                <span class="logs-pagination__summary">
                    Seite {{ $logs->currentPage() }} von {{ $logs->lastPage() }} | {{ $logs->total() }} Eintraege
                </span>
            </div>
        @endif
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
        @if($logs->hasPages())
            <div class="logs-pagination logs-pagination--bottom">
                @if($logs->onFirstPage())
                    <span class="logs-pagination__item is-disabled">Zurueck</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="logs-pagination__item">Zurueck</a>
                @endif

                @for($page = $windowStart; $page <= $windowEnd; $page++)
                    @if($page === $logs->currentPage())
                        <span class="logs-pagination__item is-active">{{ $page }}</span>
                    @else
                        <a href="{{ $logs->url($page) }}" class="logs-pagination__item">{{ $page }}</a>
                    @endif
                @endfor

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="logs-pagination__item">Weiter</a>
                @else
                    <span class="logs-pagination__item is-disabled">Weiter</span>
                @endif

                <span class="logs-pagination__summary">
                    Seite {{ $logs->currentPage() }} von {{ $logs->lastPage() }} | {{ $logs->total() }} Eintraege
                </span>
            </div>
        @endif
    </div>
@endsection
