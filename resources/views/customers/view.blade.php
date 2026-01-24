@extends('layouts.app')
@section('title', 'Add Customer')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tbody>
            <tr id="prodheader">
                <th colspan='1'>
                    <span id='title'><big>{{ $customer->short_no }} - {{ $customer->sap_no }} - <a href="{{ route('customers.city', $customer->city->id) }}">{{ $customer->city->name }}</a> - {{ $customer->name }}</big></span>
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
                            {{ Form::open(['route' => 'projects.change_status']) }}
                            {{ Form::hidden('project_id', $item->id) }}
                            <tr>
                                @php
                                    $statusName = optional($item->status)->name;
                                    $color = \App\Helpers\StatusHelper::color($statusName);
                                    $textColor = \App\Helpers\StatusHelper::textColor($statusName
                                @php
                                    $endDate = \Carbon\Carbon::parse($item->end_date)->startOfDay();
                                    $today = \Carbon\Carbon::today();
                                    $daysRemaining = $today->diffInDays($endDate, false);
                                    if ($daysRemaining > 0) {
                                        $endBg = 'green';
                                    } elseif ($daysRemaining === 0) {
                                        $endBg = 'orange';
                                    } else {
                                        $endBg = 'red';
                                    }
                                @endphp
                                <td style="text-align: left; background-color: {{ $color }}; color: {{ $textColor }};">{{ $item->dynamics_id }}</td>
                                <td style="text-align: left;"><a href="{{ route('projects.view', $item->id) }}">{{ $item->name }}</a></td>
                                <td style="text-align: left;">{{ $item->user->name }}</td>
                                <td style="text-align: left;">{{ \Carbon\Carbon::parse($item->start_date)->toDateString() }}</td>
                                <td style="text-align: left; background-color: {{ $endBg }};">
                                    {{ \Carbon\Carbon::parse($item->end_date)->toDateString() }} ({{ $daysRemaining }})
                                </td>
                                <td style="text-align: left;">{{ $item->hours ?? '-' }}</td>
                                <td style="text-align: left;">{{ Form::select('status', $status, $item->status->id) }} {{ Form::submit('Submit') }}</td>
                            </tr>
                            {{ Form::close() }}
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    {{ Form::open(['route' => 'servers.store']) }}
                    {{ Form::hidden('customer_id', $customer->id) }}
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
                        @foreach($servers as $item)
                            <tr>
                                <td>{{ $item->type }}</td>
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
                            <th>{{ Form::select('type', ['' => '', 'Produktiv' => 'Produktiv', 'Test' => 'Test', 'Schulungs' => 'Schulungs', 'Entwicklungs' => 'Entwicklungs']) }}</th>
                            <td>{{ Form::text('servername') }}</td>
                            <td>{{ Form::text('fqdn') }}</td>
                            <td>{{ Form::text('db_sid') }}</td>
                            <td>{{ Form::text('db_server') }}</td>
                            <td>{{ Form::text('ext_ip') }}</td>
                            <td>{{ Form::text('int_ip') }}</td>
                            <td></td>
                            <td>{{ Form::submit('Submit') }}</td>
                        </tr>
                    </table>
                    {{ Form::close() }}
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
                                {{ Form::open(['route' => 'remarks.store']) }}
                                {{ Form::hidden('customer_id', $customer->id) }}
                                {{ Form::textarea('remark', $remark, ['style' => 'resize:none;width:98%;']) }}
                                <br>
                                {{ Form::submit('Submit') }}
                                {{ Form::close() }}
                            </td>
                            <td>
                                <table style="width: 100%;">
                                    <tr>
                                        <th>User</th>
                                        <th>Pass</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    @foreach($credentials as $item)
                                        <tr>
                                            <td>{{ $item->username }}</td>
                                            <td>{{ $item->password }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td><a href="{{ route('credentials.delete', $item->id) }}">delete</a></td>
                                        </tr>
                                    @endforeach
                                    {{ Form::open(['route' => 'credentials.store']) }}
                                    {{ Form::hidden('customer_id', $customer->id) }}
                                    <tr>
                                        <td>{{ Form::text('username') }}</td>
                                        <td>{{ Form::text('password') }}</td>
                                        <td>{{ Form::select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'OAS' => 'OAS', 'PTC-Share' => 'PTC-Share']) }}</td>
                                        <td></td>
                                        <td>{{ Form::submit('Submit') }}</td>
                                    </tr>
                                    {{ Form::close() }}
                                </table>
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
                                        {{ Form::open(['route' => 'contact.update']) }}
                                        {{ Form::hidden('id', $contact->id) }}
                                        <td>{{ $contact->prefix }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->familyname }}</td>
                                        <td>{{ $contact->phone_mobile }}</td>
                                        <td>{{ $contact->phone_office }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ Form::text('comments', $contact->comments) }}</td>
                                        <td>{{ $contact->updated_at }}</td>
                                        <td>{{ Form::submit('Submit') }} - <a href="{{ route('contact.delete', $contact->id) }}">delete</a></td>
                                        {{ Form::close() }}
                                    </tr>
                                @endforeach
                                <tr>
                                    {{ Form::open(['route' => 'contact.create']) }}
                                    {{ Form::hidden('customer_id', $customer->id) }}
                                    <td>{{ Form::select('prefix', ['Frau' => 'Frau', 'Herr' => 'Herr', 'Prof.' => 'Prof.', 'Dr.' => 'Dr.', 'Mailbox' => 'Mailbox', 'Unbekannt' => 'Unbekannt']) }}</td>
                                    <td>{{ Form::text('name') }}</td>
                                    <td>{{ Form::text('familyname') }}</td>
                                    <td>{{ Form::text('phone_mobile') }}</td>
                                    <td>{{ Form::text('phone_office') }}</td>
                                    <td>{{ Form::text('email') }}</td>
                                    <td>{{ Form::text('comments') }}</td>
                                    <td></td>
                                    <td>{{ Form::submit('Submit') }}</td>
                                    {{ Form::close() }}
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
@endsection
