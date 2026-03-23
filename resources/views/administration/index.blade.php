@extends('layouts.app')
@section('title', 'Administration')
@section('content')
    @php($adminImportPreview = session('import_preview'))
    @php($composeImportPreview = session('compose_import_preview'))
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
                                <a href="{{ route('users.statistics', ['user_id' => $user->id]) }}" class="itsdb-action-control">Statistik</a>
                                @if(auth()->user()->hasPermission('administration', 'administration'))
                                    <a href="{{ route('administration.users.edit', $user) }}" class="itsdb-action-control">Edit</a>
                                @else
                                    @if(! auth()->user()->hasPermission('administration', 'visible'))
                                        -
                                    @endif
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
                                <a href="{{ route('administration.index', ['tab' => 'administration', 'subtab' => 'master-data']) }}" class="{{ $subtab === 'master-data' ? 'active' : '' }}">Stammdaten</a>
                                <a href="{{ route('administration.index', ['tab' => 'administration', 'subtab' => 'quality']) }}" class="{{ $subtab === 'quality' ? 'active' : '' }}">Datenqualitaet</a>
                                <a href="{{ route('administration.index', ['tab' => 'administration', 'subtab' => 'settings']) }}" class="{{ $subtab === 'settings' ? 'active' : '' }}">Einstellungen</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($subtab === 'import')
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="product-matrix-status success" style="text-align: left;">
                                    Import-Ablauf: zuerst <strong>Preview / Dry Run</strong> ausfuehren, danach den vorgeschlagenen Import mit
                                    <strong>Import bestaetigen</strong> freigeben. In der Vorschau werden Eintraege als
                                    <strong>new</strong>, <strong>update</strong>, <strong>skip</strong> oder <strong>conflict</strong> markiert.
                                </div>
                                <div class="admin-imports">
                                    <section class="admin-import-card">
                                        <h3>Compose Files</h3>
                                        <p>Uploads fuer Compose-Dateien.</p>
                                        {{ html()->form()->route('compose.upload')->class('admin-import-card__form')->attribute('enctype', 'multipart/form-data')->open() }}
                                        <div class="admin-import-card__field">
                                            <label for="compose-upload-zip">ZIP</label>
                                            {{ html()->file('compose_zip')->attribute('accept', '.zip')->id('compose-upload-zip') }}
                                        </div>
                                        <div class="admin-import-card__field">
                                            <label for="compose-upload-yml">YML-Dateien</label>
                                            {{ html()->file('compose_files[]')->attribute('multiple', true)->attribute('accept', '.yml,.yaml')->id('compose-upload-yml') }}
                                        </div>
                                        @if(auth()->user()->hasPermission('compose', 'editable'))
                                            <div>
                                                <button type="submit" name="import_mode" value="preview">Preview / Dry Run</button>
                                            </div>
                                            <p>Erzeugt zuerst nur eine Vorschau. Es wird noch nichts importiert.</p>
                                        @endif
                                        {{ html()->form()->close() }}
                                        @if(($composeImportPreview['token'] ?? null) && ($composeImportPreview['uploads'] ?? false))
                                            <div class="product-matrix-status success">
                                                Vorschau: neu {{ $composeImportPreview['summary']['new'] ?? 0 }},
                                                update {{ $composeImportPreview['summary']['update'] ?? 0 }},
                                                skip {{ $composeImportPreview['summary']['skip'] ?? 0 }},
                                                conflict {{ $composeImportPreview['summary']['conflict'] ?? 0 }}
                                            </div>
                                            <table style="width: 100%">
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Datei</th>
                                                    <th>Hinweis</th>
                                                    <th>Services</th>
                                                </tr>
                                                @foreach($composeImportPreview['uploads'] as $item)
                                                    <tr>
                                                        <td>{{ $item['action'] }}</td>
                                                        <td>{{ $item['filename'] }}</td>
                                                        <td>{{ $item['title'] }}</td>
                                                        <td>{{ $item['service_count'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            {{ html()->form()->route('compose.upload')->class('admin-import-card__form')->open() }}
                                            {{ html()->hidden('import_mode', 'confirm') }}
                                            {{ html()->hidden('preview_token', $composeImportPreview['token']) }}
                                            <button type="submit">Import bestaetigen</button>
                                            {{ html()->form()->close() }}
                                        @endif
                                    </section>

                                    <section class="admin-import-card">
                                        <h3>Produktmatrix</h3>
                                        <p>CSV-Import fuer die Produktmatrix.</p>
                                        {{ html()->form()->route('product_matrix.import')->class('admin-import-card__form')->attribute('enctype', 'multipart/form-data')->open() }}
                                        <div class="admin-import-card__field">
                                            <label for="product-matrix-import-file">CSV-Datei</label>
                                            {{ html()->file('csv_file')->attribute('accept', '.csv,text/csv')->id('product-matrix-import-file') }}
                                        </div>
                                        @if(auth()->user()->hasPermission('product_matrix', 'editable'))
                                            <div><button type="submit" name="import_mode" value="preview">Preview / Dry Run</button></div>
                                            <p>Die Vorschau fuer die Produktmatrix wird auf der Produktmatrix-Seite angezeigt und dort bestaetigt.</p>
                                        @endif
                                        {{ html()->form()->close() }}
                                    </section>

                                    <section class="admin-import-card">
                                        <h3>Kundenimport</h3>
                                        <p>Importiert `Kd.Nummer`, `SAP-Nr.` und `Ort` aus der Kundenuebersicht.</p>
                                        {{ html()->form()->route('administration.imports.customers')->class('admin-import-card__form')->attribute('enctype', 'multipart/form-data')->open() }}
                                        <div class="admin-import-card__field">
                                            <label for="customers-import-file">CSV-Datei</label>
                                            {{ html()->file('csv_file')->attribute('accept', '.csv,text/csv')->id('customers-import-file') }}
                                        </div>
                                        <div class="admin-import-card__field">
                                            <label for="customers-import-country">Land-Fallback fuer neue Orte</label>
                                            {{ html()->select('fallback_country_code', ['de' => 'DE', 'at' => 'AT', 'ch' => 'CH', 'lu' => 'LU'], 'de')->id('customers-import-country') }}
                                        </div>
                                        @if(auth()->user()->hasPermission('administration', 'editable'))
                                            <div><button type="submit" name="import_mode" value="preview">Preview / Dry Run</button></div>
                                            <p>Zeigt vor dem Schreiben an, welche Kunden neu angelegt oder uebersprungen werden.</p>
                                        @endif
                                        {{ html()->form()->close() }}
                                        @if(($adminImportPreview['type'] ?? null) === 'customers')
                                            <div class="product-matrix-status success">
                                                Vorschau: neu {{ $adminImportPreview['summary']['new'] ?? 0 }},
                                                update {{ $adminImportPreview['summary']['update'] ?? 0 }},
                                                skip {{ $adminImportPreview['summary']['skip'] ?? 0 }},
                                                conflict {{ $adminImportPreview['summary']['conflict'] ?? 0 }}
                                            </div>
                                            <table style="width: 100%">
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Hinweis</th>
                                                    <th>Details</th>
                                                </tr>
                                                @foreach($adminImportPreview['plan'] as $item)
                                                    <tr>
                                                        <td>{{ $item['action'] }}</td>
                                                        <td>{{ $item['title'] }}</td>
                                                        <td>{{ collect($item['details'] ?? [])->map(fn ($value, $key) => $key.': '.$value)->implode(', ') }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            {{ html()->form()->route('administration.imports.customers')->class('admin-import-card__form')->open() }}
                                            {{ html()->hidden('import_mode', 'confirm') }}
                                            {{ html()->hidden('preview_type', 'customers') }}
                                            {{ html()->hidden('preview_token', $adminImportPreview['token']) }}
                                            <button type="submit">Import bestaetigen</button>
                                            {{ html()->form()->close() }}
                                        @endif
                                    </section>

                                    <section class="admin-import-card">
                                        <h3>OrbisU Server Import</h3>
                                        <p>Ordnet Server ueber die Short-Nummer dem Kunden zu und aktualisiert nur bei neuerem `Aktualisiert`-Wert.</p>
                                        {{ html()->form()->route('administration.imports.orbisu_servers')->class('admin-import-card__form')->attribute('enctype', 'multipart/form-data')->open() }}
                                        <div class="admin-import-card__field">
                                            <label for="orbisu-import-file">CSV-Datei</label>
                                            {{ html()->file('csv_file')->attribute('accept', '.csv,text/csv')->id('orbisu-import-file') }}
                                        </div>
                                        @if(auth()->user()->hasPermission('administration', 'editable'))
                                            <div><button type="submit" name="import_mode" value="preview">Preview / Dry Run</button></div>
                                            <p>Prueft zuerst, ob Server neu, aktualisiert, uebersprungen oder konflikthaft sind.</p>
                                        @endif
                                        {{ html()->form()->close() }}
                                        @if(($adminImportPreview['type'] ?? null) === 'orbisu_servers')
                                            <div class="product-matrix-status success">
                                                Vorschau: neu {{ $adminImportPreview['summary']['new'] ?? 0 }},
                                                update {{ $adminImportPreview['summary']['update'] ?? 0 }},
                                                skip {{ $adminImportPreview['summary']['skip'] ?? 0 }},
                                                conflict {{ $adminImportPreview['summary']['conflict'] ?? 0 }}
                                            </div>
                                            <table style="width: 100%">
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Hinweis</th>
                                                    <th>Details</th>
                                                </tr>
                                                @foreach($adminImportPreview['plan'] as $item)
                                                    <tr>
                                                        <td>{{ $item['action'] }}</td>
                                                        <td>{{ $item['title'] }}</td>
                                                        <td>{{ collect($item['details'] ?? [])->map(fn ($value, $key) => $key.': '.$value)->implode(', ') }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            {{ html()->form()->route('administration.imports.orbisu_servers')->class('admin-import-card__form')->open() }}
                                            {{ html()->hidden('import_mode', 'confirm') }}
                                            {{ html()->hidden('preview_type', 'orbisu_servers') }}
                                            {{ html()->hidden('preview_token', $adminImportPreview['token']) }}
                                            <button type="submit">Import bestaetigen</button>
                                            {{ html()->form()->close() }}
                                        @endif
                                    </section>

                                    <section class="admin-import-card">
                                        <h3>OAS-Import</h3>
                                        <p>Importiert OAS-Server ueber `Projekt / SAP Nr`, Hostname und IP-Adresse und verknuepft sie mit bestehenden Kunden.</p>
                                        {{ html()->form()->route('administration.imports.oas_servers')->class('admin-import-card__form')->attribute('enctype', 'multipart/form-data')->open() }}
                                        <div class="admin-import-card__field">
                                            <label for="oas-import-file">CSV-Datei</label>
                                            {{ html()->file('csv_file')->attribute('accept', '.csv,text/csv')->id('oas-import-file') }}
                                        </div>
                                        @if(auth()->user()->hasPermission('administration', 'editable'))
                                            <div><button type="submit" name="import_mode" value="preview">Preview / Dry Run</button></div>
                                            <p>Zeigt vorab, welche OAS-Server neu angelegt oder aktualisiert werden wuerden.</p>
                                        @endif
                                        {{ html()->form()->close() }}
                                        @if(($adminImportPreview['type'] ?? null) === 'oas_servers')
                                            <div class="product-matrix-status success">
                                                Vorschau: neu {{ $adminImportPreview['summary']['new'] ?? 0 }},
                                                update {{ $adminImportPreview['summary']['update'] ?? 0 }},
                                                skip {{ $adminImportPreview['summary']['skip'] ?? 0 }},
                                                conflict {{ $adminImportPreview['summary']['conflict'] ?? 0 }}
                                            </div>
                                            <table style="width: 100%">
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Hinweis</th>
                                                    <th>Details</th>
                                                </tr>
                                                @foreach($adminImportPreview['plan'] as $item)
                                                    <tr>
                                                        <td>{{ $item['action'] }}</td>
                                                        <td>{{ $item['title'] }}</td>
                                                        <td>{{ collect($item['details'] ?? [])->map(fn ($value, $key) => $key.': '.$value)->implode(', ') }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                            {{ html()->form()->route('administration.imports.oas_servers')->class('admin-import-card__form')->open() }}
                                            {{ html()->hidden('import_mode', 'confirm') }}
                                            {{ html()->hidden('preview_type', 'oas_servers') }}
                                            {{ html()->hidden('preview_token', $adminImportPreview['token']) }}
                                            <button type="submit">Import bestaetigen</button>
                                            {{ html()->form()->close() }}
                                        @endif
                                    </section>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @elseif($subtab === 'master-data')
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="2">Projektstatis verwalten</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(auth()->user()->hasPermission('administration', 'editable'))
                            <tr>
                                {{ html()->form()->route('administration.statuses.store')->open() }}
                                <td>{{ html()->text('name', old('name'))->attribute('placeholder', 'Neuen Status anlegen') }}</td>
                                <td>{{ html()->submit('Status anlegen') }}</td>
                                {{ html()->form()->close() }}
                            </tr>
                        @endif

                        @forelse($statuses as $status)
                            <tr>
                                @if(auth()->user()->hasPermission('administration', 'editable'))
                                    {{ html()->form()->route('administration.statuses.update', $status)->open() }}
                                    <td>{{ html()->text('name', $status->name) }}</td>
                                    <td>{{ html()->submit('Speichern') }}</td>
                                    {{ html()->form()->close() }}
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
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="3">Orte verwalten</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Ort</th>
                            <th>Land</th>
                            <th>Kunden</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cities as $city)
                            <tr>
                                {{ html()->form()->route('administration.cities.update', $city)->open() }}
                                <td>{{ $city->name }}</td>
                                <td>{{ html()->text('country_code', $city->country_code)->attribute('maxlength', 2)->attribute('style', 'width:60px') }}</td>
                                <td>{{ $city->customers_count }}</td>
                                <td>{{ html()->submit('Speichern') }}</td>
                                {{ html()->form()->close() }}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Noch keine Orte vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="3">Serverarten verwalten</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Serverart</th>
                            <th>Server</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(auth()->user()->hasPermission('administration', 'editable'))
                            <tr>
                                {{ html()->form()->route('administration.server_kinds.store')->open() }}
                                <td>{{ html()->text('name', old('name'))->attribute('placeholder', 'Neue Serverart') }}</td>
                                <td>-</td>
                                <td>{{ html()->submit('Serverart anlegen') }}</td>
                                {{ html()->form()->close() }}
                            </tr>
                        @endif
                        @forelse($serverKinds as $serverKind)
                            <tr>
                                @if(auth()->user()->hasPermission('administration', 'editable'))
                                    {{ html()->form()->route('administration.server_kinds.update', $serverKind)->open() }}
                                    <td>{{ html()->text('name', $serverKind->name) }}</td>
                                    <td>{{ $serverKind->servers_count }}</td>
                                    <td>{{ html()->submit('Speichern') }}</td>
                                    {{ html()->form()->close() }}
                                @else
                                    <td>{{ $serverKind->name }}</td>
                                    <td>{{ $serverKind->servers_count }}</td>
                                    <td>-</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Noch keine Serverarten vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="3">Betriebssysteme verwalten</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Betriebssystem</th>
                            <th>Server</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(auth()->user()->hasPermission('administration', 'editable'))
                            <tr>
                                {{ html()->form()->route('administration.operating_systems.store')->open() }}
                                <td>{{ html()->text('name', old('name'))->attribute('placeholder', 'Neues Betriebssystem') }}</td>
                                <td>-</td>
                                <td>{{ html()->submit('Betriebssystem anlegen') }}</td>
                                {{ html()->form()->close() }}
                            </tr>
                        @endif
                        @forelse($operatingSystems as $operatingSystem)
                            <tr>
                                @if(auth()->user()->hasPermission('administration', 'editable'))
                                    {{ html()->form()->route('administration.operating_systems.update', $operatingSystem)->open() }}
                                    <td>{{ html()->text('name', $operatingSystem->name) }}</td>
                                    <td>{{ $operatingSystem->servers_count }}</td>
                                    <td>{{ html()->submit('Speichern') }}</td>
                                    {{ html()->form()->close() }}
                                @else
                                    <td>{{ $operatingSystem->name }}</td>
                                    <td>{{ $operatingSystem->servers_count }}</td>
                                    <td>-</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Noch keine Betriebssysteme vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($subtab === 'quality')
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="2">Datenqualitaets-Center</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Kunden ohne Ort</td>
                            <td>{{ $customersWithoutCity->count() }}</td>
                        </tr>
                        <tr>
                            <td>Server ohne Betriebssystem</td>
                            <td>{{ $serversWithoutOperatingSystem->count() }}</td>
                        </tr>
                        <tr>
                            <td>Server ohne Serverart</td>
                            <td>{{ $serversWithoutServerKind->count() }}</td>
                        </tr>
                        <tr>
                            <td>Projekte ohne Stunden</td>
                            <td>{{ $projectsWithoutHours->count() }}</td>
                        </tr>
                        <tr>
                            <td>Doppelte SAP-Nummern</td>
                            <td>{{ $duplicateSapCustomers->groupBy('sap_no')->count() }}</td>
                        </tr>
                        <tr>
                            <td>Doppelte Short-Nummern</td>
                            <td>{{ $duplicateShortCustomers->groupBy('short_no')->count() }}</td>
                        </tr>
                        <tr>
                            <td>Credentials ohne Server</td>
                            <td>{{ $credentialsWithoutServers->count() }}</td>
                        </tr>
                        <tr>
                            <td>Compose-Dateien ohne Container-Zuordnung</td>
                            <td>{{ $composeWithoutContainers->count() }}</td>
                        </tr>
                    </tbody>
                </table>
                <table id="missing-cities" class="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="5">Kunden ohne Ort</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{ html()->form()->route('administration.cities.store')->open() }}
                            <td>{{ html()->text('name', old('name'))->attribute('placeholder', 'Neuen Ort anlegen') }}</td>
                            <td>{{ html()->text('country_code', old('country_code', 'de'))->attribute('maxlength', 2)->attribute('style', 'width:60px') }}</td>
                            <td colspan="3">{{ html()->submit('Ort anlegen') }}</td>
                            {{ html()->form()->close() }}
                        </tr>
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th>Short</th>
                            <th>SAP</th>
                            <th>Kunde</th>
                            <th>Ort zuweisen</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customersWithoutCity as $customer)
                            <tr>
                                {{ html()->form()->route('administration.customers.city.update', $customer)->open() }}
                                <td>{{ $customer->short_no }}</td>
                                <td>{{ $customer->sap_no }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>
                                    <div class="admin-city-autocomplete" data-city-autocomplete>
                                        <input
                                            type="text"
                                            name="city_name"
                                            placeholder="Ort suchen"
                                            autocomplete="off"
                                            data-city-input
                                        >
                                        <button type="button" class="admin-city-autocomplete__toggle" data-city-toggle aria-label="Ort auswaehlen">v</button>
                                        <div class="admin-city-autocomplete__menu" data-city-menu hidden>
                                            @foreach($cities as $city)
                                                <button
                                                    type="button"
                                                    class="admin-city-autocomplete__option"
                                                    data-city-option
                                                    data-city-id="{{ $city->id }}"
                                                    data-city-name="{{ $city->name }}"
                                                >
                                                    {{ strtoupper($city->country_code) }} - {{ $city->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" name="city_id" data-city-id-input>
                                </td>
                                <td>{{ html()->submit('Ort speichern') }}</td>
                                {{ html()->form()->close() }}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Keine Kunden ohne City-Zuordnung vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="5">Server ohne Betriebssystem</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Server</th>
                            <th>Kunde</th>
                            <th>Typ</th>
                            <th>IP</th>
                            <th>Erstellt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serversWithoutOperatingSystem as $server)
                            <tr>
                                <td><a href="{{ route('servers.view', $server->id) }}">{{ $server->servername ?: '-' }}</a></td>
                                <td>{{ $server->customer?->name ?? '-' }}</td>
                                <td>{{ $server->type ?: '-' }}</td>
                                <td>{{ $server->int_ip ?: $server->ext_ip ?: '-' }}</td>
                                <td>{{ optional($server->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Keine Server ohne Betriebssystem vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="5">Server ohne Serverart</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Server</th>
                            <th>Kunde</th>
                            <th>Typ</th>
                            <th>IP</th>
                            <th>Erstellt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serversWithoutServerKind as $server)
                            <tr>
                                <td><a href="{{ route('servers.view', $server->id) }}">{{ $server->servername ?: '-' }}</a></td>
                                <td>{{ $server->customer?->name ?? '-' }}</td>
                                <td>{{ $server->type ?: '-' }}</td>
                                <td>{{ $server->int_ip ?: $server->ext_ip ?: '-' }}</td>
                                <td>{{ optional($server->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Keine Server ohne Serverart vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="6">Projekte ohne Stunden</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Projekt</th>
                            <th>Kunde</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Ende</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projectsWithoutHours as $project)
                            <tr>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->customer?->name ?? '-' }}</td>
                                <td>{{ $project->user?->name ?? '-' }}</td>
                                <td>{{ $project->status?->name ?? '-' }}</td>
                                <td>{{ optional($project->end_date)->format('Y-m-d') ?? '-' }}</td>
                                <td><a href="{{ route('projects.view', $project) }}">Projekt oeffnen</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Keine Projekte ohne Stunden vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="4">Doppelte SAP-Nummern</th>
                        </tr>
                        <tr id="prodheader">
                            <th>SAP</th>
                            <th>Short</th>
                            <th>Kunde</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($duplicateSapCustomers as $customer)
                            <tr>
                                <td>{{ $customer->sap_no }}</td>
                                <td>{{ $customer->short_no }}</td>
                                <td>{{ $customer->name }}</td>
                                <td><a href="{{ route('customers.view', $customer) }}">Kunde oeffnen</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Keine doppelten SAP-Nummern vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="4">Doppelte Short-Nummern</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Short</th>
                            <th>SAP</th>
                            <th>Kunde</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($duplicateShortCustomers as $customer)
                            <tr>
                                <td>{{ $customer->short_no }}</td>
                                <td>{{ $customer->sap_no }}</td>
                                <td>{{ $customer->name }}</td>
                                <td><a href="{{ route('customers.view', $customer) }}">Kunde oeffnen</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Keine doppelten Short-Nummern vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="4">Credentials ohne Server</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Username</th>
                            <th>Typ</th>
                            <th>Kunden-ID</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credentialsWithoutServers as $credential)
                            <tr>
                                <td>{{ $credential->username }}</td>
                                <td>{{ $credential->type }}</td>
                                <td>{{ $credential->customer_id }}</td>
                                <td><a href="{{ route('customers.view', $credential->customer_id) }}">Kunde oeffnen</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Keine Credentials ohne Server vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="4">Compose-Dateien ohne Container-Zuordnung</th>
                        </tr>
                        <tr id="prodheader">
                            <th>Titel</th>
                            <th>Datei</th>
                            <th>Importdatum</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($composeWithoutContainers as $compose)
                            <tr>
                                <td>{{ $compose->title }}</td>
                                <td>{{ $compose->compose_filename }}</td>
                                <td>{{ optional($compose->orig_date)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td><a href="{{ route('compose.show', $compose->compose_filename) }}">Compose oeffnen</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Keine Compose-Dateien ohne Container-Zuordnung vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <script>
                    (function() {
                        function closeMenu(wrapper) {
                            var menu = wrapper.querySelector('[data-city-menu]');
                            if (!menu) return;
                            menu.hidden = true;
                        }

                        function openMenu(wrapper) {
                            var menu = wrapper.querySelector('[data-city-menu]');
                            if (!menu) return;
                            menu.hidden = false;
                        }

                        function filterOptions(wrapper) {
                            var input = wrapper.querySelector('[data-city-input]');
                            var hidden = wrapper.parentElement.querySelector('[data-city-id-input]');
                            var term = ((input && input.value) || '').trim().toLowerCase();

                            if (hidden) {
                                hidden.value = '';
                            }

                            wrapper.querySelectorAll('[data-city-option]').forEach(function(option) {
                                var cityName = (option.getAttribute('data-city-name') || '').toLowerCase();
                                option.hidden = term !== '' && cityName.indexOf(term) === -1;
                            });
                        }

                        function selectOption(wrapper, option) {
                            var input = wrapper.querySelector('[data-city-input]');
                            var hidden = wrapper.parentElement.querySelector('[data-city-id-input]');

                            if (input) {
                                input.value = option.getAttribute('data-city-name') || '';
                            }
                            if (hidden) {
                                hidden.value = option.getAttribute('data-city-id') || '';
                            }

                            closeMenu(wrapper);
                        }

                        function initCityAutocomplete() {
                            document.querySelectorAll('[data-city-autocomplete]').forEach(function(wrapper) {
                                var input = wrapper.querySelector('[data-city-input]');
                                var toggle = wrapper.querySelector('[data-city-toggle]');
                                var menu = wrapper.querySelector('[data-city-menu]');

                                if (!input || !toggle || !menu) {
                                    return;
                                }

                                input.addEventListener('focus', function() {
                                    filterOptions(wrapper);
                                    openMenu(wrapper);
                                });

                                input.addEventListener('input', function() {
                                    filterOptions(wrapper);
                                    openMenu(wrapper);
                                });

                                toggle.addEventListener('click', function() {
                                    if (menu.hidden) {
                                        filterOptions(wrapper);
                                        openMenu(wrapper);
                                    } else {
                                        closeMenu(wrapper);
                                    }
                                });

                                wrapper.querySelectorAll('[data-city-option]').forEach(function(option) {
                                    option.addEventListener('click', function() {
                                        selectOption(wrapper, option);
                                    });
                                });
                            });

                            document.addEventListener('click', function(event) {
                                document.querySelectorAll('[data-city-autocomplete]').forEach(function(wrapper) {
                                    if (!wrapper.contains(event.target)) {
                                        closeMenu(wrapper);
                                    }
                                });
                            });
                        }

                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initCityAutocomplete);
                        } else {
                            initCityAutocomplete();
                        }
                    })();
                </script>
            @else
                <table id="pouetbox_prodmain">
                    <thead>
                        <tr id="prodheader">
                            <th colspan="2">Einstellungen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{ html()->form()->route('administration.settings.update')->open() }}
                            <td>Registrierung aktiv</td>
                            <td>
                                {{ html()->checkbox('registration_enabled', $registrationEnabled, 1) }}
                                @if(auth()->user()->hasPermission('administration', 'administration'))
                                    {{ html()->submit('Speichern') }}
                                @endif
                            </td>
                            {{ html()->form()->close() }}
                        </tr>
                    </tbody>
                </table>
            @endif
        @endif
    </div>
@endsection
