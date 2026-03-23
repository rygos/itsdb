@extends('layouts.app')
@section('title', 'User Statistics')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="4">
                    <span id="title"><big>User-Statistik fuer {{ $statsUser->name }}</big></span>
                    <div id="nfo">
                        {{ $statsUser->email }}
                        | Registriert: {{ optional($statsUser->created_at)->format('d.m.Y H:i') ?? '-' }}
                        | Letzter Login: {{ optional($statsUser->last_login_at)->format('d.m.Y H:i') ?? '-' }}
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><strong>Kunden</strong><br>{{ $statistics['customers_count'] }}</td>
                <td><strong>Projekte</strong><br>{{ $statistics['projects_count'] }}</td>
                <td><strong>Offene Projekte</strong><br>{{ $statistics['open_projects_count'] }}</td>
                <td><strong>Abgeschlossene Projekte</strong><br>{{ $statistics['finished_projects_count'] }}</td>
            </tr>
            <tr>
                <td><strong>Geplante Stunden</strong><br>{{ $statistics['planned_hours'] }}</td>
                <td><strong>Server</strong><br>{{ $statistics['servers_count'] }}</td>
                <td><strong>Dokumente</strong><br>{{ $statistics['documents_count'] }}</td>
                <td><strong>Dokumentgroesse</strong><br>{{ number_format($statistics['documents_size'] / 1024, 2, ',', '.') }} KB</td>
            </tr>
            <tr>
                <td><strong>Logs gesamt</strong><br>{{ $statistics['logs_count'] }}</td>
                <td><strong>Logs 30 Tage</strong><br>{{ $statistics['recent_logs_count'] }}</td>
                <td><strong>Abwesenheiten</strong><br>{{ $statistics['vacations_count'] }}</td>
                <td><strong>Abwesenheitstage</strong><br>{{ number_format($statistics['vacation_units'] / 2, 1, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="3">Berechtigungen</th>
            </tr>
            <tr id="prodheader">
                <th>Bereich</th>
                <th>Level</th>
                <th>Bezeichnung</th>
            </tr>
            </thead>
            <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission['area'] }}</td>
                    <td>{{ $permission['level'] }}</td>
                    <td>{{ $permission['label'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th>Server nach Typ</th>
                <th>Logs nach Bereich</th>
                <th>Logs nach Typ</th>
                <th>Abwesenheiten nach Typ</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <table style="width: 100%">
                        <tr>
                            <th>Typ</th>
                            <th>Anzahl</th>
                        </tr>
                        @forelse($serverTypes as $item)
                            <tr>
                                <td>{{ $item->label }}</td>
                                <td>{{ $item->aggregate }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Keine Server vorhanden.</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table style="width: 100%">
                        <tr>
                            <th>Bereich</th>
                            <th>Anzahl</th>
                        </tr>
                        @forelse($logSections as $item)
                            <tr>
                                <td>{{ $item->label }}</td>
                                <td>{{ $item->aggregate }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Keine Logs vorhanden.</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table style="width: 100%">
                        <tr>
                            <th>Typ</th>
                            <th>Anzahl</th>
                        </tr>
                        @forelse($logTypes as $item)
                            <tr>
                                <td>{{ $item->label }}</td>
                                <td>{{ $item->aggregate }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Keine Logs vorhanden.</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table style="width: 100%">
                        <tr>
                            <th>Typ</th>
                            <th>Eintraege</th>
                            <th>Tage</th>
                        </tr>
                        @forelse($vacationTypes as $item)
                            <tr>
                                <td>{{ $item['label'] }}</td>
                                <td>{{ $item['aggregate'] }}</td>
                                <td>{{ number_format($item['day_units'] / 2, 1, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Keine Abwesenheiten vorhanden.</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
            </tr>
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="5">Letzte Projekte</th>
            </tr>
            <tr id="prodheader">
                <th>Name</th>
                <th>Kunde</th>
                <th>Status</th>
                <th>Stunden</th>
                <th>Angelegt</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentProjects as $project)
                <tr>
                    <td><a href="{{ route('projects.view', $project) }}">{{ $project->name }}</a></td>
                    <td>
                        @if($project->customer)
                            <a href="{{ route('customers.view', $project->customer) }}">{{ $project->customer->name }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ optional($project->status)->name ?? '-' }}</td>
                    <td>{{ $project->hours ?? '-' }}</td>
                    <td>{{ optional($project->created_at)->format('d.m.Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Keine Projekte vorhanden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="4">Letzte Kunden</th>
            </tr>
            <tr id="prodheader">
                <th>Nr.</th>
                <th>Name</th>
                <th>Ort</th>
                <th>Angelegt</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentCustomers as $customer)
                <tr>
                    <td>{{ $customer->short_no }}</td>
                    <td><a href="{{ route('customers.view', $customer) }}">{{ $customer->name }}</a></td>
                    <td>{{ $customer->city?->name ?? 'Kein Ort' }}</td>
                    <td>{{ optional($customer->created_at)->format('d.m.Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Keine Kunden vorhanden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="5">Letzte Server</th>
            </tr>
            <tr id="prodheader">
                <th>Name</th>
                <th>Kunde</th>
                <th>Typ</th>
                <th>Serverart</th>
                <th>Angelegt</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentServers as $server)
                <tr>
                    <td><a href="{{ route('servers.view', $server->id) }}">{{ $server->servername ?: '-' }}</a></td>
                    <td>{{ $server->customer?->name ?? '-' }}</td>
                    <td>{{ $server->type ?: '-' }}</td>
                    <td>{{ $server->serverKind?->name ?? '-' }}</td>
                    <td>{{ optional($server->created_at)->format('d.m.Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Keine Server vorhanden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="5">Letzte Dokumente</th>
            </tr>
            <tr id="prodheader">
                <th>Datei</th>
                <th>Kunde</th>
                <th>Typ</th>
                <th>Groesse</th>
                <th>Hochgeladen</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentDocuments as $document)
                <tr>
                    <td>{{ $document->original_name }}</td>
                    <td>{{ $document->customer?->name ?? '-' }}</td>
                    <td>{{ $document->mime_type ?? '-' }}</td>
                    <td>{{ $document->formatted_size }}</td>
                    <td>{{ $document->formatted_uploaded_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Keine Dokumente vorhanden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th colspan="4">Letzte Abwesenheiten</th>
            </tr>
            <tr id="prodheader">
                <th>Typ</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Tage</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentVacations as $vacation)
                <tr>
                    <td>{{ \App\Models\Vacation::typeOptions()[$vacation->type] ?? $vacation->type }}</td>
                    <td>{{ optional($vacation->start_date)->format('d.m.Y') ?? '-' }}</td>
                    <td>{{ optional($vacation->end_date)->format('d.m.Y') ?? '-' }}</td>
                    <td>{{ $vacation->display_days }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Keine Abwesenheiten vorhanden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <table id="pouetbox_prodmain" data-sortable="true">
            <thead>
            <tr id="prodheader">
                <th colspan="4">Letzte Logs</th>
            </tr>
            <tr id="prodheader">
                <th>Zeitpunkt</th>
                <th>Bereich</th>
                <th>Typ</th>
                <th>Meldung</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentLogs as $log)
                <tr>
                    <td>{{ optional($log->created_at)->format('d.m.Y H:i') ?? '-' }}</td>
                    <td>{{ $log->section }}</td>
                    <td>{{ $log->type }}</td>
                    <td>{{ $log->msg }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Keine Logs vorhanden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
