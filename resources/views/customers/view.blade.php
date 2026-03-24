@extends('layouts.app')
@section('title', 'Add Customer')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                        <span id='title'><big>{{ $customer->short_no }} - {{ $customer->sap_no }} -
                            @if($customer->city)
                                <a href="{{ route('customers.city', $customer->city->id) }}">{{ $customer->city->name }}</a>
                            @else
                                Kein Ort
                            @endif
                            - {{ $customer->name }}</big></span>
                        @if(auth()->user()->hasPermission('customers', 'editable'))
                            <a href="{{ route('customers.edit', $customer) }}">Bearbeiten</a>
                        @endif
                    </div>
                    <div id='nfo'></div>
                </th>
            </tr>
            <tr>
                <td>
                    <table id="stattable">

                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%">
                        @if(auth()->user()->hasPermission('projects', 'editable'))
                            <tr>
                                <td colspan="7" style="text-align: left; padding-bottom: 12px;">
                                    <button type="button" data-modal-target="#project-create-modal">Projekt hinzufuegen</button>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>Dynamics ID</th>
                            <th>Name</th>
                            <th>User</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                        @foreach($projects as $item)
                            <tr>
                                @php
                                    $statusName = optional($item->status)->name;
                                    $color = \App\Helpers\StatusHelper::color($statusName);
                                    $textColor = \App\Helpers\StatusHelper::textColor($statusName);
                                    $canManageProject = $item->user_id === auth()->id();
                                @endphp
                                @php
                                    $endDate = \Carbon\Carbon::parse($item->end_date)->startOfDay();
                                    $today = \Carbon\Carbon::today();
                                    $daysRemaining = $today->diffInDays($endDate, false);
                                    $deadlineAccent = \App\Helpers\StatusHelper::deadlineAccent($daysRemaining);
                                @endphp
                                <td style="text-align: left; background-color: {{ $color }}; color: {{ $textColor }};">{{ $item->dynamics_id }}</td>
                                <td style="text-align: left;"><a href="{{ route('projects.view', $item->id) }}">{{ $item->name }}</a></td>
                                <td style="text-align: left;">{{ $item->user->name }}</td>
                                <td style="text-align: left;">{{ \Carbon\Carbon::parse($item->start_date)->toDateString() }}</td>
                                <td style="text-align: left; background-color: {{ $deadlineAccent['background'] }}; color: {{ $deadlineAccent['color'] }}; box-shadow: inset 0 0 0 1px {{ $deadlineAccent['border'] }};">
                                    {{ \Carbon\Carbon::parse($item->end_date)->toDateString() }} ({{ $daysRemaining }})
                                </td>
                                <td style="text-align: left;">{{ $item->hours ?? '-' }}</td>
                                <td style="text-align: left;">
                                    @if($canManageProject)
                                        {{ html()->form()->route('projects.change_status')->class('js-project-status-form')->open() }}
                                        {{ html()->hidden('project_id', $item->id) }}
                                        {{ html()->hidden('finished_end_date_action')->attribute('data-finished-end-date-action', 'true') }}
                                        {{ html()->select('status', $status, $item->status_id)
                                            ->attribute('data-project-status-select', 'true')
                                            ->attribute('data-project-name', $item->name)
                                            ->attribute('data-current-status', (string) $item->status_id)
                                            ->attribute('data-current-end-date', \Carbon\Carbon::parse($item->end_date)->toDateString())
                                            ->attribute('data-current-end-date-display', \Carbon\Carbon::parse($item->end_date)->format('d.m.Y'))
                                            ->attribute('data-finished-status-id', $finishedStatusId ?? '')
                                        }}
                                        {{ html()->submit('Submit') }}
                                        {{ html()->form()->close() }}
                                    @else
                                        {{ optional($item->status)->name ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    {{ html()->form()->route('servers.store')->open() }}
                    {{ html()->hidden('customer_id', $customer->id) }}
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
                        @foreach($servers as $item)
                            <tr>
                                <td>{{ $item->type }}</td>
                                <td>{{ optional($item->serverKind)->name ?? '-' }}</td>
                                <td>{{ optional($item->operatingSystem)->name ?? '-' }}</td>
                                <td><a href="{{ route('servers.view', $item->id) }}">{{ $item->servername }}</a></td>
                                <td>{{ $item->fqdn }}</td>
                                <td>{{ $item->db_sid }}</td>
                                <td>{{ $item->db_server }}</td>
                                <td>{{ $item->ext_ip }}</td>
                                <td>{{ $item->int_ip }}</td>
                                <td>
                                    @php
                                        $cert_s = openssl_x509_parse($item->server_cert_raw);
                                        $cert_i = openssl_x509_parse($customer->intermediate_cert_raw);
                                        $cert_r = openssl_x509_parse($customer->root_cert_raw);
                                        $cert_k = openssl_pkey_get_private($item->private_key_raw);
                                    @endphp
                                    @if($cert_s)
                                        @if(is_array($cert_s['subject']['CN']))
                                            @foreach($cert_s['subject']['CN'] as $key=>$value)
                                                @if(strtolower($value) == strtolower($item->fqdn))
                                                    @if(\Carbon\Carbon::parse($cert_s['validTo_time_t'])->greaterThan(\Carbon\Carbon::now()->addDays(30)) )
                                                        &#9989;
                                                        @break
                                                    @else
                                                        &#10067;
                                                        @break
                                                    @endif
                                                @else
                                                    &#10067;
                                                @endif
                                            @endforeach
                                        @else
                                            @if(strtolower($cert_s['subject']['CN']) == strtolower($item->fqdn))
                                                @if(\Carbon\Carbon::parse($cert_s['validTo_time_t'])->greaterThan(\Carbon\Carbon::now()->addDays(30)) )
                                                    &#9989;
                                                @else
                                                    &#10067;
                                                @endif
                                            @else
                                                &#10067;
                                            @endif
                                        @endif
                                    @else
                                        &#10060;
                                    @endif
                                    @if($cert_i)
                                        {{-- todo:Prüfe auf Validität mit rootCert --}}
                                        @if(\Carbon\Carbon::parse($cert_i['validTo_time_t'])->greaterThan(\Carbon\Carbon::now()->addDays(30)) )
                                            &#9989;
                                        @else
                                            &#10067;
                                        @endif
                                    @else
                                        &#10060;
                                    @endif
                                    @if($cert_r)
                                        @php
                                            $cr = $cert_r['subject']['CN'];
                                            $cs = $cert_s['issuer']['CN'] ?? 'no';
                                        @endphp
                                        @if($cert_i)
                                            @php
                                                $ci_s = $cert_i['subject']['CN'];
                                                $ci_i = $cert_i['issuer']['CN'];
                                            @endphp
                                            @if($cs == $ci_s and $ci_i == $cr)
                                                @if(\Carbon\Carbon::parse($cert_r['validTo_time_t'])->greaterThan(\Carbon\Carbon::now()->addDays(30)) )
                                                    &#9989;
                                                @else
                                                    &#10067;
                                                @endif
                                            @else
                                                &#10067;
                                            @endif
                                        @else
                                            @if($cr == $cs)
                                                @if(\Carbon\Carbon::parse($cert_r['validTo_time_t'])->greaterThan(\Carbon\Carbon::now()->addDays(30)) )
                                                    &#9989;
                                                @else
                                                    &#10067;
                                                @endif
                                            @else
                                                &#10067;
                                            @endif
                                        @endif
                                    @else
                                        &#10060;
                                    @endif
                                    @if($cert_k)
                                        @php $cert_k_details = openssl_pkey_get_details($cert_k); @endphp
                                        @if(openssl_x509_check_private_key($item->server_cert_raw, $item->private_key_raw))
                                            &#9989;
                                        @else
                                            &#10067;
                                        @endif
                                    @else
                                        &#10060;
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                                <th>{{ html()->select('type', ['' => '', 'Produktiv' => 'Produktiv', 'Test' => 'Test', 'Schulungs' => 'Schulungs', 'Entwicklungs' => 'Entwicklungs', 'Integration' => 'Integration', 'Auswerte' => 'Auswerte']) }}</th>
                            <td>{{ html()->select('server_kind_id', $serverKindOptions) }}</td>
                            <td>{{ html()->select('operating_system_id', $operatingSystemOptions) }}</td>
                            <td>{{ html()->text('servername') }}</td>
                            <td>{{ html()->text('fqdn') }}</td>
                            <td>{{ html()->text('db_sid') }}</td>
                            <td>{{ html()->text('db_server') }}</td>
                            <td>{{ html()->text('ext_ip') }}</td>
                            <td>{{ html()->text('int_ip') }}</td>
                            <td></td>
                            <td>{{ html()->submit('Submit') }}</td>
                        </tr>
                    </table>
                    {{ html()->form()->close() }}
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%">
                        <tr>
                            <th style="width: 50%">Remark</th>
                            <th>Credentials</th>
                        </tr>
                        <tr>
                            <td>
                                {{ html()->form()->route('remarks.store')->open() }}
                                {{ html()->hidden('customer_id', $customer->id) }}
                                {{ html()->textarea('remark', $remark)->attribute('style', 'resize:none;width:98%;') }}
                                <br>
                                {{ html()->submit('Submit') }}
                                {{ html()->form()->close() }}
                            </td>
                            <td>
                                <table style="width: 100%;">
                                    <tr>
                                        <th>User</th>
                                        <th>Pass</th>
                                        <th>ENAM</th>
                                        <th>Type</th>
                                        <th>Server</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <button type="button" data-modal-target="#credential-create-modal">Credential hinzufuegen</button>
                                        </td>
                                    </tr>
                                    @foreach($credentials as $item)
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
                                            <td>
                                                <button
                                                    type="button"
                                                    class="itsdb-copy-button"
                                                    data-enam-copy
                                                    data-enam-username="{{ $item->username }}"
                                                    data-enam-password="{{ $item->password }}"
                                                    data-copy-tooltip="Kopiert"
                                                    title="ENAM-Zeile kopieren"
                                                >
                                                    <span aria-hidden="true">&#9000;</span>
                                                </button>
                                            </td>
                                            <td>{{ $item->type }}</td>
                                            <td>
                                                @if($item->servers->isEmpty())
                                                    Alle / Nicht zugeordnet
                                                @else
                                                    {{ $item->servers->pluck('servername')->implode(', ') }}
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at ? $item->created_at->format('d.m.Y') : '-' }}</td>
                                            <td>
                                                <div class="itsdb-actions">
                                                    <button type="button" data-modal-target="#credential-edit-modal-{{ $item->id }}">bearbeiten</button>
                                                    <a href="{{ route('credentials.delete', $item->id) }}" class="itsdb-action-control">delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>Kundenspezifische Dokumente</th>
                        </tr>
                        <tr>
                            <td>
                                <table style="width: 100%;">
                                    <tr>
                                        <th>Dateiname</th>
                                        <th>Dateibeschreibung</th>
                                        <th>Dateigroesse</th>
                                        <th>Upload Datum</th>
                                        <th>Aktion</th>
                                    </tr>
                                    @forelse($documents as $document)
                                        <tr>
                                            <td style="text-align: left;">{{ $document->original_name }}</td>
                                            <td style="text-align: left;">{{ $document->description ?: '-' }}</td>
                                            <td style="text-align: left;">{{ $document->formatted_size }}</td>
                                            <td style="text-align: left;">{{ $document->formatted_uploaded_at }}</td>
                                            <td style="text-align: left;">
                                                <div class="itsdb-actions">
                                                    @if($document->is_image)
                                                        <button type="button" data-modal-target="#customer-document-preview-modal-{{ $document->id }}">view</button>
                                                    @endif
                                                    <a href="{{ route('customer_documents.download', $document->id) }}" class="itsdb-action-control">download</a>
                                                    <button type="button" data-modal-target="#customer-document-delete-modal-{{ $document->id }}">loeschen</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">Keine Dokumente vorhanden.</td>
                                        </tr>
                                    @endforelse
                                </table>
                                <br>
                                {{ html()->form()->route('customer_documents.store')->id('customer-document-upload-form')->attribute('enctype', 'multipart/form-data')->open() }}
                                {{ html()->hidden('customer_id', $customer->id) }}
                                <table style="width: 100%;">
                                    <tr>
                                        <th>Datei</th>
                                        <th>Beschreibung</th>
                                        <th>Aktion</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ html()->file('document')->id('customer-document-input')->attribute('accept', 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.zip') }}
                                            <div class="customer-document-paste-box" id="customer-document-paste-box" tabindex="0">
                                                Bild aus Zwischenablage hier einfuegen mit Strg+V
                                            </div>
                                            <div class="customer-document-paste-status" id="customer-document-paste-status" aria-live="polite"></div>
                                        </td>
                                        <td>{{ html()->text('description') }}</td>
                                        <td>{{ html()->submit('Upload') }}</td>
                                    </tr>
                                </table>
                                {{ html()->form()->close() }}
                            </td>
                        </tr>
                        <tr>
                            <table style="width: 100%;">
                                <tr>
                                    <th>Prefix</th>
                                    <th>FirstName</th>
                                    <th>Family Name / Title</th>
                                    <th>Phone Mobile</th>
                                    <th>Phone Office</th>
                                    <th>EMail</th>
                                    <th>Comments</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                                @foreach($contacts as $contact)
                                    <tr>
                                        {{ html()->form()->route('contact.update')->open() }}
                                        {{ html()->hidden('id', $contact->id) }}
                                        <td>{{ $contact->prefix }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->familyname }}</td>
                                        <td>{{ $contact->phone_mobile }}</td>
                                        <td>{{ $contact->phone_office }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ html()->text('comments', $contact->comments) }}</td>
                                        <td>{{ $contact->updated_at }}</td>
                                        <td>
                                            <div class="itsdb-actions">
                                                {{ html()->submit('Submit') }}
                                                <a href="{{ route('contact.delete', $contact->id) }}" class="itsdb-action-control">delete</a>
                                            </div>
                                        </td>
                                        {{ html()->form()->close() }}
                                    </tr>
                                @endforeach
                                <tr>
                                    {{ html()->form()->route('contact.create')->open() }}
                                    {{ html()->hidden('customer_id', $customer->id) }}
                                    <td>{{ html()->select('prefix', ['Frau' => 'Frau', 'Herr' => 'Herr', 'Prof.' => 'Prof.', 'Dr.' => 'Dr.', 'Mailbox' => 'Mailbox', 'Unbekannt' => 'Unbekannt']) }}</td>
                                    <td>{{ html()->text('name') }}</td>
                                    <td>{{ html()->text('familyname') }}</td>
                                    <td>{{ html()->text('phone_mobile') }}</td>
                                    <td>{{ html()->text('phone_office') }}</td>
                                    <td>{{ html()->text('email') }}</td>
                                    <td>{{ html()->text('comments') }}</td>
                                    <td></td>
                                    <td>{{ html()->submit('Submit') }}</td>
                                    {{ html()->form()->close() }}
                                </tr>
                            </table>
                        </tr>
                        <tr>
                            <th>Logs</th>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%">
                                    <tr>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Log</th>
                                        <th>Date</th>
                                    </tr>
                                    @foreach($logs as $item)
                                        <tr>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->msg }}</td>
                                            <td>{!! nl2br($item->created_at) !!}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="itsdb-modal" id="credential-create-modal" aria-hidden="true">
        <div class="itsdb-modal__dialog">
            <div class="itsdb-modal__header">
                <div class="itsdb-modal__title">Credential hinzufuegen</div>
                <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
            </div>
            <div class="itsdb-modal__body">
                {{ html()->form()->route('credentials.store')->open() }}
                {{ html()->hidden('customer_id', $customer->id) }}
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
                            {{ html()->select('server_ids[]', $servers->pluck('servername', 'id')->toArray(), null)->attribute('multiple', true)->attribute('size', max(3, $servers->count())) }}
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
        <div class="itsdb-modal" id="credential-edit-modal-{{ $item->id }}" aria-hidden="true">
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
                                {{ html()->select('server_ids[]', $servers->pluck('servername', 'id')->toArray(), $item->servers->pluck('id')->map(function ($id) { return (string) $id; })->all())->attribute('multiple', true)->attribute('size', max(3, $servers->count())) }}
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

    @foreach($documents as $document)
        @if($document->is_image)
            <div class="itsdb-modal" id="customer-document-preview-modal-{{ $document->id }}" aria-hidden="true">
                <div class="itsdb-modal__dialog itsdb-modal__dialog--image-preview">
                    <div class="itsdb-modal__header">
                        <div class="itsdb-modal__title">{{ $document->original_name }}</div>
                        <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
                    </div>
                    <div class="itsdb-modal__body itsdb-modal__body--image-preview">
                        <img
                            src="{{ route('customer_documents.preview', $document->id) }}"
                            alt="{{ $document->original_name }}"
                            class="customer-document-preview-image"
                        >
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @foreach($documents as $document)
        <div class="itsdb-modal" id="customer-document-delete-modal-{{ $document->id }}" aria-hidden="true">
            <div class="itsdb-modal__dialog">
                <div class="itsdb-modal__header">
                    <div class="itsdb-modal__title">Dokument loeschen</div>
                    <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
                </div>
                <div class="itsdb-modal__body">
                    <p style="margin-bottom: 12px;">Soll das Dokument "{{ $document->original_name }}" wirklich geloescht werden?</p>
                    <div class="itsdb-actions">
                        <a href="{{ route('customer_documents.delete', $document->id) }}" class="itsdb-action-control">Ja, loeschen</a>
                        <button type="button" data-modal-close>Abbrechen</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if(auth()->user()->hasPermission('projects', 'editable'))
        <div class="itsdb-modal" id="project-create-modal" aria-hidden="true">
            <div class="itsdb-modal__dialog">
                <div class="itsdb-modal__header">
                    <div class="itsdb-modal__title">Projekt hinzufuegen</div>
                    <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
                </div>
                <div class="itsdb-modal__body">
                    {{ html()->form()->route('projects.store')->open() }}
                    {{ html()->hidden('customer', $customer->id) }}
                    <table class="itsdb-modal__grid">
                        <tr>
                            <td class="itsdb-modal__grid-label">Dynamics Nummer</td>
                            <td>{{ html()->text('dynamics_id', old('dynamics_id')) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Projektname</td>
                            <td>{{ html()->text('name', old('name')) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Startdatum</td>
                            <td>{{ html()->input('date', 'start_date', old('start_date', now()->toDateString())) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Enddatum</td>
                            <td>{{ html()->input('date', 'end_date', old('end_date', now()->toDateString())) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Stunden</td>
                            <td>{{ html()->input('number', 'hours', old('hours'))->attribute('min', 0) }}</td>
                        </tr>
                    </table>
                    <div class="itsdb-modal__footer">
                        {{ html()->submit('Speichern') }}
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    @endif

    <div class="itsdb-modal" id="project-finished-end-date-modal" aria-hidden="true">
        <div class="itsdb-modal__dialog">
            <div class="itsdb-modal__header">
                <div class="itsdb-modal__title">Enddatum beim Abschluss anpassen</div>
                <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
            </div>
            <div class="itsdb-modal__body">
                <p id="project-finished-end-date-message" style="margin-bottom: 12px;">
                    Dieses Projekt hat aktuell ein anderes Enddatum als heute.
                </p>
                <div class="itsdb-actions">
                    <button type="button" id="project-finished-end-date-confirm">Ja, auf aktuellen Tag setzen</button>
                    <button type="button" id="project-finished-end-date-keep">Alten Tag lassen</button>
                    <button type="button" data-modal-close>Abbrechen</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var modal = document.getElementById('project-finished-end-date-modal');
            var message = document.getElementById('project-finished-end-date-message');
            var confirmButton = document.getElementById('project-finished-end-date-confirm');
            var keepButton = document.getElementById('project-finished-end-date-keep');
            var pendingForm = null;
            var pendingSelect = null;
            var previousStatusValue = null;

            if (!modal || !message || !confirmButton || !keepButton) {
                return;
            }

            function setModalState(isOpen) {
                modal.style.display = isOpen ? 'flex' : 'none';
                modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            }

            function resetPendingSelection() {
                if (pendingSelect && previousStatusValue !== null) {
                    pendingSelect.value = previousStatusValue;
                }

                pendingForm = null;
                pendingSelect = null;
                previousStatusValue = null;
            }

            function submitPendingForm(action) {
                if (!pendingForm) {
                    return;
                }

                var actionInput = pendingForm.querySelector('[data-finished-end-date-action]');
                if (actionInput) {
                    actionInput.value = action;
                }

                setModalState(false);
                pendingForm.submit();
                pendingForm = null;
                pendingSelect = null;
                previousStatusValue = null;
            }

            document.querySelectorAll('[data-project-status-select]').forEach(function(select) {
                select.addEventListener('change', function() {
                    var finishedStatusId = select.getAttribute('data-finished-status-id');
                    var currentEndDate = select.getAttribute('data-current-end-date');
                    var today = new Date();
                    var todayString = [
                        today.getFullYear(),
                        String(today.getMonth() + 1).padStart(2, '0'),
                        String(today.getDate()).padStart(2, '0')
                    ].join('-');

                    if (!finishedStatusId || select.value !== finishedStatusId || currentEndDate === todayString) {
                        return;
                    }

                    pendingForm = select.closest('form');
                    pendingSelect = select;
                    previousStatusValue = select.getAttribute('data-current-status') || select.defaultValue;

                    var projectName = select.getAttribute('data-project-name') || 'Das Projekt';
                    var currentEndDateDisplay = select.getAttribute('data-current-end-date-display') || currentEndDate;
                    var todayDisplay = [
                        String(today.getDate()).padStart(2, '0'),
                        String(today.getMonth() + 1).padStart(2, '0'),
                        today.getFullYear()
                    ].join('.');

                    message.textContent = '"' + projectName + '" hat aktuell das Enddatum ' + currentEndDateDisplay + '. Soll das Enddatum beim Setzen auf FINISHED auf den heutigen Tag ' + todayDisplay + ' gesetzt werden?';
                    setModalState(true);
                });
            });

            confirmButton.addEventListener('click', function() {
                submitPendingForm('set_today');
            });

            keepButton.addEventListener('click', function() {
                submitPendingForm('keep');
            });

            modal.querySelectorAll('[data-modal-close]').forEach(function(closeTrigger) {
                closeTrigger.addEventListener('click', function() {
                    setModalState(false);
                    resetPendingSelection();
                });
            });

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    setModalState(false);
                    resetPendingSelection();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                    resetPendingSelection();
                }
            });
        })();
    </script>

    <script>
        (function() {
            var input = document.getElementById('customer-document-input');
            var pasteBox = document.getElementById('customer-document-paste-box');
            var status = document.getElementById('customer-document-paste-status');

            if (!input || !pasteBox || !status || typeof DataTransfer === 'undefined') {
                return;
            }

            function setStatus(message, isError) {
                status.textContent = message || '';
                status.classList.toggle('is-error', !!isError);
                status.classList.toggle('is-success', !!message && !isError);
            }

            function setSelectedFile(file) {
                var transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;
            }

            function updateSelectedFileStatus() {
                if (!input.files || !input.files.length) {
                    setStatus('', false);
                    return;
                }

                setStatus('Ausgewaehlt: ' + input.files[0].name, false);
            }

            function handlePaste(event) {
                var items = event.clipboardData && event.clipboardData.items ? event.clipboardData.items : [];
                var imageItem = null;

                for (var i = 0; i < items.length; i++) {
                    if (items[i].type && items[i].type.indexOf('image/') === 0) {
                        imageItem = items[i];
                        break;
                    }
                }

                if (!imageItem) {
                    setStatus('Keine Bilddaten in der Zwischenablage gefunden.', true);
                    return;
                }

                var blob = imageItem.getAsFile();
                if (!blob) {
                    setStatus('Das Bild konnte nicht aus der Zwischenablage gelesen werden.', true);
                    return;
                }

                var mimeType = blob.type || 'image/png';
                var extension = mimeType.split('/')[1] || 'png';
                var file = new File([blob], 'clipboard-image-' + Date.now() + '.' + extension, { type: mimeType });

                setSelectedFile(file);
                setStatus('Bild aus Zwischenablage uebernommen: ' + file.name, false);
                event.preventDefault();
            }

            pasteBox.addEventListener('click', function() {
                pasteBox.focus();
            });
            pasteBox.addEventListener('paste', handlePaste);
            input.addEventListener('change', updateSelectedFileStatus);
            updateSelectedFileStatus();
        })();
    </script>

    <script>
        (function() {
            function fallbackCopyText(value) {
                var element = document.createElement('textarea');
                element.value = value;
                element.setAttribute('readonly', 'readonly');
                element.style.position = 'absolute';
                element.style.left = '-9999px';
                document.body.appendChild(element);
                element.select();
                document.execCommand('copy');
                document.body.removeChild(element);
            }

            function copyText(value) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(value);
                }

                fallbackCopyText(value);
                return Promise.resolve();
            }

            function flashCopyState(button) {
                if (!button) return;

                var originalTitle = button.getAttribute('data-original-title') || button.getAttribute('title') || '';
                if (!button.getAttribute('data-original-title')) {
                    button.setAttribute('data-original-title', originalTitle);
                }

                button.setAttribute('title', 'Kopiert');
                button.classList.add('is-copied');
                button.classList.add('show-copy-tooltip');

                window.setTimeout(function() {
                    button.setAttribute('title', originalTitle);
                    button.classList.remove('is-copied');
                    button.classList.remove('show-copy-tooltip');
                }, 1200);
            }

            function createUuid() {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return window.crypto.randomUUID();
                }

                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(character) {
                    var random = Math.random() * 16 | 0;
                    var value = character === 'x' ? random : (random & 0x3 | 0x8);
                    return value.toString(16);
                });
            }

            document.querySelectorAll('[data-enam-copy]').forEach(function(button) {
                button.addEventListener('click', function() {
                    var payload = '<ENAM_UUID>' + createUuid() + ':' + (button.getAttribute('data-enam-username') || '') + ':' + (button.getAttribute('data-enam-password') || '');
                    copyText(payload).then(function() {
                        flashCopyState(button);
                    });
                });
            });
        })();
    </script>
@endsection
