@extends('layouts.app')
@section('title', 'Administration')
@section('content')
    <div id="prodpagecontainer" class="admin-page">
        <table id="pouetbox_prodmain" class="admin-table-shell">
            <thead>
                <tr id="prodheader">
                    <th colspan="2">
                        <span id="title"><big>Administration</big></span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="admin-tabs">
                            <a href="{{ route('administration.index', ['tab' => 'users']) }}" class="{{ $tab === 'users' ? 'active' : '' }}">Benutzerverwaltung</a>
                            <a href="{{ route('administration.index', ['tab' => 'statuses']) }}" class="{{ $tab === 'statuses' ? 'active' : '' }}">Statis</a>
                            <a href="{{ route('administration.index', ['tab' => 'administration', 'subtab' => 'import']) }}" class="{{ $tab === 'administration' ? 'active' : '' }}">Administration</a>
                        </div>

                        @if(session('status'))
                            <div class="admin-status-message">{{ session('status') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="admin-error-message">{{ $errors->first() }}</div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        @if($tab === 'users')
            <table id="pouetbox_prodmain" data-sortable="true">
                <thead>
                    <tr id="prodheader">
                        <th>Username</th>
                        <th>Email Adresse</th>
                        <th>Registrierungsdatum</th>
                        <th>Letzte Nutzung</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ optional($user->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>{{ optional($user->last_login_at)->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>
                                @if(auth()->user()->hasPermission('administration', 'administration'))
                                    <a href="{{ route('administration.users.edit', $user) }}">Edit</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Keine Benutzer vorhanden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($tab === 'statuses')
            <table id="pouetbox_prodmain">
                <thead>
                    <tr id="prodheader">
                        <th colspan="2">Projektstatis verwalten</th>
                    </tr>
                </thead>
                <tbody>
                    @if(auth()->user()->hasPermission('administration', 'editable'))
                        <tr>
                            {!! Form::open(['route' => 'administration.statuses.store']) !!}
                            <td>{!! Form::text('name', old('name'), ['placeholder' => 'Neuen Status anlegen']) !!}</td>
                            <td>{{ Form::submit('Status anlegen') }}</td>
                            {!! Form::close() !!}
                        </tr>
                    @endif

                    @forelse($statuses as $status)
                        <tr>
                            @if(auth()->user()->hasPermission('administration', 'editable'))
                                {!! Form::open(['route' => ['administration.statuses.update', $status]]) !!}
                                <td>{!! Form::text('name', $status->name) !!}</td>
                                <td>{{ Form::submit('Speichern') }}</td>
                                {!! Form::close() !!}
                            @else
                                <td colspan="2">{{ $status->name }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Noch keine Statis vorhanden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <table id="pouetbox_prodmain" class="admin-table-shell">
                <thead>
                    <tr id="prodheader">
                        <th colspan="2">Administration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <div class="admin-tabs admin-subtabs">
                                <a href="{{ route('administration.index', ['tab' => 'administration', 'subtab' => 'import']) }}" class="{{ $subtab === 'import' ? 'active' : '' }}">Import</a>
                                <a href="{{ route('administration.index', ['tab' => 'administration', 'subtab' => 'settings']) }}" class="{{ $subtab === 'settings' ? 'active' : '' }}">Einstellungen</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($subtab === 'settings')
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="2">Einstellungen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {!! Form::open(['route' => 'administration.settings.update']) !!}
                            <td>Registrierung aktiv</td>
                            <td>
                                {!! Form::checkbox('registration_enabled', 1, $registrationEnabled) !!}
                                @if(auth()->user()->hasPermission('administration', 'administration'))
                                    {{ Form::submit('Speichern') }}
                                @endif
                            </td>
                            {!! Form::close() !!}
                        </tr>
                    </tbody>
                </table>
            @else
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th>Import</th>
                            <th>Beschreibung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {!! Form::open(['route' => 'compose.upload', 'files' => true]) !!}
                                <div><strong>Compose Files</strong></div>
                                <div>ZIP: {!! Form::file('compose_zip', ['accept' => '.zip']) !!}</div>
                                <div>YML: {!! Form::file('compose_files[]', ['multiple' => true, 'accept' => '.yml,.yaml']) !!}</div>
                                @if(auth()->user()->hasPermission('compose', 'editable'))
                                    <div>{{ Form::submit('Upload Compose') }}</div>
                                @endif
                                {!! Form::close() !!}
                            </td>
                            <td>Uploads fuer Compose-Dateien.</td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::open(['route' => 'product_matrix.import', 'files' => true]) !!}
                                <div><strong>Produktenmatrix</strong></div>
                                <div>{!! Form::file('csv_file', ['accept' => '.csv,text/csv']) !!}</div>
                                @if(auth()->user()->hasPermission('product_matrix', 'editable'))
                                    <div>{{ Form::submit('Import Produktmatrix') }}</div>
                                @endif
                                {!! Form::close() !!}
                            </td>
                            <td>CSV-Import fuer die Produktmatrix.</td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::open(['route' => 'administration.imports.customers', 'files' => true]) !!}
                                <div><strong>Kundenimport</strong></div>
                                <div>{!! Form::file('csv_file', ['accept' => '.csv,text/csv']) !!}</div>
                                <div>
                                    Land-Fallback fuer neue Orte:
                                    {!! Form::select('fallback_country_code', ['de' => 'DE', 'at' => 'AT', 'ch' => 'CH', 'lu' => 'LU'], 'de') !!}
                                </div>
                                @if(auth()->user()->hasPermission('administration', 'editable'))
                                    <div>{{ Form::submit('Import Kunden') }}</div>
                                @endif
                                {!! Form::close() !!}
                            </td>
                            <td>Importiert `Kd.Nummer`, `SAP-Nr.` und `Ort` aus der Kundenuebersicht.</td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::open(['route' => 'administration.imports.orbisu_servers', 'files' => true]) !!}
                                <div><strong>OrbisU Server Import</strong></div>
                                <div>{!! Form::file('csv_file', ['accept' => '.csv,text/csv']) !!}</div>
                                @if(auth()->user()->hasPermission('administration', 'editable'))
                                    <div>{{ Form::submit('Import OrbisU Server') }}</div>
                                @endif
                                {!! Form::close() !!}
                            </td>
                            <td>Ordnet Server ueber die Short-Nummer dem Kunden zu und aktualisiert nur bei neuerem `Aktualisiert`-Wert.</td>
                        </tr>
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th>Ort</th>
                            <th>Land</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cities as $city)
                            <tr>
                                {!! Form::open(['route' => ['administration.cities.update', $city]]) !!}
                                <td>{{ $city->name }}</td>
                                <td>{!! Form::text('country_code', $city->country_code, ['maxlength' => 2, 'style' => 'width:60px']) !!}</td>
                                <td>{{ Form::submit('Speichern') }}</td>
                                {!! Form::close() !!}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Noch keine Orte vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        @endif
    </div>
@endsection
