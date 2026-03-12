@extends('layouts.app')
@section('title', 'Serverinfo')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th colspan='1'>
                        <span id='title'><big><a href="{{ route('customers.view', $server->customer->id) }}">{{ $server->customer->short_no }} - {{ $server->customer->sap_no }} - {{ $server->customer->name }}</a></big></span>
                        <div id='nfo'>{{ $server->servername }}</div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table style="width: 100%">
                            <tr>
                                <th>Informations</th>
                            </tr>
                            <tr>
                                <td>
                                    {{ Form::open(['route' => 'servers.update']) }}
                                    {{ Form::hidden('server_id', $server->id) }}
                                    <table style="width: 100%">
                                        <tr>
                                            <th>Type</th>
                                            <th>Servername</th>
                                            <th>FQDN</th>
                                            <th>DB-SID</th>
                                            <th>DB-Server</th>
                                            <th>ext. IP</th>
                                            <th>int. IP</th>
                                            <th>Certificate</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td>{{ Form::select('type', ['' => '', 'Produktiv' => 'Produktiv', 'Test' => 'Test', 'Schulungs' => 'Schulungs', 'Entwicklungs' => 'Entwicklungs'], $server->type) }}</td>
                                            <td>{{ Form::text('servername', $server->servername) }}</td>
                                            <td>{{ Form::text('fqdn', $server->fqdn) }}</td>
                                            <td>{{ Form::text('db_sid', $server->db_sid) }}</td>
                                            <td>{{ Form::text('db_server', $server->db_server) }}</td>
                                            <td>{{ Form::text('ext_ip', $server->ext_ip) }}</td>
                                            <td>{{ Form::text('int_ip', $server->int_ip) }}</td>
                                            <td></td>
                                            <td>{{ Form::submit('Submit') }}</td>
                                        </tr>
                                    </table>
                                    {{ Form::close() }}
                                </td>
                            </tr>
                            <tr>
                                <th>Zugeordnete Credentials</th>
                            </tr>
                            <tr>
                                <td>
                                    <table style="width: 100%">
                                        <tr>
                                            <th>User</th>
                                            <th>Pass</th>
                                            <th>Type</th>
                                            <th>Server</th>
                                            <th>Erstellt</th>
                                            <th>Aktion</th>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <button type="button" data-modal-target="#server-credential-create-modal">Credential fuer diesen Server hinzufuegen</button>
                                            </td>
                                        </tr>
                                        @forelse($credentials as $item)
                                            <tr>
                                                <td>{{ $item->username }}</td>
                                                <td>{{ $item->password }}</td>
                                                <td>{{ $item->type }}</td>
                                                <td>{{ $item->servers->pluck('servername')->implode(', ') }}</td>
                                                <td>{{ $item->created_at }}</td>
                                                <td>
                                                    <div class="itsdb-actions">
                                                        <button type="button" data-modal-target="#server-credential-edit-modal-{{ $item->id }}">bearbeiten</button>
                                                        <a href="{{ route('credentials.delete', $item->id) }}">delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">Keine Credentials mit diesem Server verknuepft.</td>
                                            </tr>
                                        @endforelse
                                    </table>
                                </td>
                            </tr>
                            @include('servers._partials.config')
                            @include('servers._partials.certificates')
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="itsdb-modal" id="server-credential-create-modal" aria-hidden="true">
        <div class="itsdb-modal__dialog">
            <div class="itsdb-modal__header">
                <div class="itsdb-modal__title">Credential fuer {{ $server->servername }} hinzufuegen</div>
                <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
            </div>
            <div class="itsdb-modal__body">
                {{ Form::open(['route' => 'credentials.store']) }}
                {{ Form::hidden('customer_id', $server->customer->id) }}
                <table class="itsdb-modal__grid">
                    <tr>
                        <td class="itsdb-modal__grid-label">User</td>
                        <td>{{ Form::text('username') }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Passwort</td>
                        <td>{{ Form::text('password') }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Typ</td>
                        <td>{{ Form::select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'Orbis Auth' => 'Orbis Auth', 'OAS' => 'OAS', 'OAS Admin' => 'OAS Admin', 'PTC-Share' => 'PTC-Share']) }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Server</td>
                        <td>
                            {{ Form::select('server_ids[]', $server->customer->servers->pluck('servername', 'id')->toArray(), [(string) $server->id], ['multiple' => true, 'size' => max(3, $server->customer->servers->count())]) }}
                        </td>
                    </tr>
                </table>
                <div class="itsdb-modal__footer">
                    {{ Form::submit('Speichern') }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    @foreach($credentials as $item)
        <div class="itsdb-modal" id="server-credential-edit-modal-{{ $item->id }}" aria-hidden="true">
            <div class="itsdb-modal__dialog">
                <div class="itsdb-modal__header">
                    <div class="itsdb-modal__title">Credential bearbeiten</div>
                    <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
                </div>
                <div class="itsdb-modal__body">
                    {{ Form::open(['route' => 'credentials.update']) }}
                    {{ Form::hidden('id', $item->id) }}
                    <table class="itsdb-modal__grid">
                        <tr>
                            <td class="itsdb-modal__grid-label">User</td>
                            <td>{{ Form::text('username', $item->username) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Passwort</td>
                            <td>{{ Form::text('password', $item->password) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Typ</td>
                            <td>{{ Form::select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'Orbis Auth' => 'Orbis Auth', 'OAS' => 'OAS', 'OAS Admin' => 'OAS Admin', 'PTC-Share' => 'PTC-Share'], $item->type) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Server</td>
                            <td>
                                {{ Form::select('server_ids[]', $server->customer->servers->pluck('servername', 'id')->toArray(), $item->servers->pluck('id')->map(function ($id) { return (string) $id; })->all(), ['multiple' => true, 'size' => max(3, $server->customer->servers->count())]) }}
                            </td>
                        </tr>
                    </table>
                    <div class="itsdb-modal__footer">
                        {{ Form::submit('Aktualisieren') }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    @endforeach
@endsection
