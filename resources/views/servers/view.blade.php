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
                            @include('servers._partials.config')
                            @include('servers._partials.certificates')
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
