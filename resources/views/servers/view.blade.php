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
                                    {{ html()->form()->route('servers.update')->open() }}
                                    {{ html()->hidden('server_id', $server->id) }}
                                    <table style="width: 100%">
                                        <tr>
                                            <th>Type</th>
                                            <th>Serverart</th>
                                            <th>OS</th>
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
                                            <td>{{ html()->select('type', ['' => '', 'Produktiv' => 'Produktiv', 'Test' => 'Test', 'Schulungs' => 'Schulungs', 'Entwicklungs' => 'Entwicklungs', 'Integration' => 'Integration', 'Auswerte' => 'Auswerte'], $server->type) }}</td>
                                            <td>{{ html()->select('server_kind_id', $serverKindOptions, $server->server_kind_id) }}</td>
                                            <td>{{ html()->select('operating_system_id', $operatingSystemOptions, $server->operating_system_id) }}</td>
                                            <td>{{ html()->text('servername', $server->servername) }}</td>
                                            <td>{{ html()->text('fqdn', $server->fqdn) }}</td>
                                            <td>{{ html()->text('db_sid', $server->db_sid) }}</td>
                                            <td>{{ html()->text('db_server', $server->db_server) }}</td>
                                            <td>{{ html()->text('ext_ip', $server->ext_ip) }}</td>
                                            <td>{{ html()->text('int_ip', $server->int_ip) }}</td>
                                            <td></td>
                                            <td>{{ html()->submit('Submit') }}</td>
                                        </tr>
                                    </table>
                                    {{ html()->form()->close() }}
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
                                                <td>
                                                    @include('_partials.credential-copy-field', [
                                                        'copyValue' => $item->username,
                                                    ])
                                                </td>
                                                <td>
                                                    @include('_partials.credential-copy-field', [
                                                        'copyValue' => $item->password,
                                                        'isPassword' => true,
                                                    ])
                                                </td>
                                                <td>{{ $item->type }}</td>
                                                <td>{{ $item->servers->pluck('servername')->implode(', ') }}</td>
                                                <td>{{ $item->created_at }}</td>
                                                <td>
                                                    <div class="itsdb-actions">
                                                        <button type="button" data-modal-target="#server-credential-edit-modal-{{ $item->id }}">bearbeiten</button>
                                                        <a href="{{ route('credentials.delete', $item->id) }}" class="itsdb-action-control">delete</a>
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
                {{ html()->form()->route('credentials.store')->open() }}
                {{ html()->hidden('customer_id', $server->customer->id) }}
                <table class="itsdb-modal__grid">
                    <tr>
                        <td class="itsdb-modal__grid-label">User</td>
                        <td>{{ html()->text('username') }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Passwort</td>
                        <td>{{ html()->text('password') }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Typ</td>
                        <td>{{ html()->select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'Orbis Auth' => 'Orbis Auth', 'OAS' => 'OAS', 'OAS Admin' => 'OAS Admin', 'PTC-Share' => 'PTC-Share']) }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Server</td>
                        <td>
                            <div class="itsdb-server-picker">
                                @foreach($server->customer->servers as $serverOption)
                                    <label class="itsdb-server-picker__option">
                                        <input type="checkbox" name="server_ids[]" value="{{ $serverOption->id }}" @checked((string) $serverOption->id === (string) $server->id)>
                                        <span>{{ $serverOption->servername }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="itsdb-modal__footer">
                    {{ html()->submit('Speichern') }}
                </div>
                {{ html()->form()->close() }}
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
                    {{ html()->form()->route('credentials.update')->open() }}
                    {{ html()->hidden('id', $item->id) }}
                    <table class="itsdb-modal__grid">
                        <tr>
                            <td class="itsdb-modal__grid-label">User</td>
                            <td>{{ html()->text('username', $item->username) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Passwort</td>
                            <td>{{ html()->text('password', $item->password) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Typ</td>
                            <td>{{ html()->select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'Orbis Auth' => 'Orbis Auth', 'OAS' => 'OAS', 'OAS Admin' => 'OAS Admin', 'PTC-Share' => 'PTC-Share'], $item->type) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Server</td>
                            <td>
                                @php($selectedServerIds = $item->servers->pluck('id')->map(fn ($id) => (string) $id)->all())
                                <div class="itsdb-server-picker">
                                    @foreach($server->customer->servers as $serverOption)
                                        <label class="itsdb-server-picker__option">
                                            <input type="checkbox" name="server_ids[]" value="{{ $serverOption->id }}" @checked(in_array((string) $serverOption->id, $selectedServerIds, true))>
                                            <span>{{ $serverOption->servername }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="itsdb-modal__footer">
                        {{ html()->submit('Aktualisieren') }}
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    @endforeach
@endsection
