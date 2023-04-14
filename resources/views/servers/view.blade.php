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
                                <th>Server-Config</th>
                            </tr>
                            <tr>
                                <td>
                                    <table id="pouetbox_prodmain" style="width: 100%">
                                        <tr>
                                            <th>docker-compose.yml</th>
                                            <th>.env</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table id="pouetbox_prodmain">
                                                    {{ Form::open(['route' => ['servers.add_composer', $server->id]]) }}
                                                    <tr>
                                                        <th>Docker-Compose</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Add docker-compose: {{ Form::select('compose', $compose_select) }}</td>
                                                        <td>{{ Form::submit('Submit') }}</td>
                                                    </tr>
                                                    @foreach($compose as $item)
                                                    <tr>
                                                        <td><a href="{{ route('compose.show', $item->composer->compose_filename) }}">{{ $item->composer->title }} @if(!is_null($item->composer->title_alternatives)) {{ ' ('.$item->composer->title_alternatives.')' }} @endif</a></td>
                                                        <td><a href="{{ route('servers.del_composer', [$server->id, $item->composer_id]) }}">delete</a></td>
                                                    </tr>
                                                    @endforeach
                                                    {{ Form::close() }}
                                                </table>
                                            </td>
                                            <td>
                                                <table id="pouetbox_prodmain">
                                                    <tr>
                                                        <th>Key</th>
                                                        <th>Variable</th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><a href="{{ route('env.generate', $server->id) }}">Generate from docker-compose</a></td>
                                                    </tr>
                                                    {{ Form::open(['route' => ['env.update', $server->id]]) }}
                                                    @foreach($env as $item)
                                                        <tr>
                                                            @php
                                                                if($item->needed == 1){
                                                                    $color = 'red';
                                                                }else{
                                                                    $color = 'orange';
                                                                }
                                                            @endphp
                                                            <td style="color: {{ $color }}">{{ $item->key }}</td>
                                                            <td>{{ Form::text($item->key, $item->value) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="2">{{ Form::submit('Submit') }}</td>
                                                    </tr>
                                                    {{ Form::close() }}
                                                </table>
                                            </td>
                                        </tr>
                                        {{ Form::open(['route' => 'servers.update_serverconfig']) }}
                                        {{ Form::hidden('server_id', $server->id) }}
                                        <tr>
                                            <td>
                                                {{ Form::textarea('docker_compose', $server->docker_compose_raw, ['cols' => 100]) }}
                                            </td>
                                            <td>
                                                {{ Form::textarea('env', $server->env_raw, ['cols' => 100]) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ route('compose.generate', $server->id) }}">Generate docker-compose.yml</a></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">{{ Form::submit('Submit') }}</td>
                                        </tr>
                                        {{ Form::close() }}
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th>Certificates</th>
                            </tr>
                            <tr>
                                <td>
                                    <table style="width: 100%">
                                        <tr>
                                            <th>Server</th>
                                            <th>Intermediate</th>
                                            <th>CA/Root</th>
                                            <th>Private Key</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                @if($certs['server'] != false)
                                                <table>
                                                    <tr>
                                                        <td>Subject (CN):</td>
                                                        <td>
                                                            @if(is_array($certs['server']['subject']['CN']))
                                                                @foreach($certs['server']['subject']['CN'] as $key=>$value)
                                                                    @if($key == 0)
                                                                        {{ $value }}
                                                                    @else
                                                                        <br>{{ $value }}
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                {{ $certs['server']['subject']['CN'] }}
                                                            @endif

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Issuer (CN):</td>
                                                        <td>{{ $certs['server']['issuer']['CN'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Valid From:</td>
                                                        <td>{{ \Carbon\Carbon::parse($certs['server']['validFrom_time_t']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Valid To:</td>
                                                        <td>{{ \Carbon\Carbon::parse($certs['server']['validTo_time_t']) }} <br> ({{ \Carbon\Carbon::parse($certs['server']['validTo_time_t'])->diffForHumans() }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Signature Type:</td>
                                                        <td>{{ $certs['server']['signatureTypeSN'] }}</td>
                                                    </tr>
                                                </table>
                                                @endif
                                            </td>
                                            <td>
                                                @if($certs['intermediate'] != false)
                                                    <table>
                                                        <tr>
                                                            <td>Subject (CN):</td>
                                                            <td>{{ @$certs['intermediate']['subject']['CN'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Issuer (CN):</td>
                                                            <td>{{ @$certs['intermediate']['issuer']['CN'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valid From:</td>
                                                            <td>{{ @\Carbon\Carbon::parse($certs['intermediate']['validFrom_time_t']) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valid To:</td>
                                                            <td>{{ @\Carbon\Carbon::parse($certs['intermediate']['validTo_time_t']) }} <br> ({{ @\Carbon\Carbon::parse($certs['intermediate']['validTo_time_t'])->diffForHumans() }})</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Signature Type:</td>
                                                            <td>{{ @$certs['intermediate']['signatureTypeSN'] }}</td>
                                                        </tr>
                                                    </table>
                                                @endif
                                            </td>
                                            <td>
                                                @if($certs['root'] != false)
                                                <table>
                                                    <tr>
                                                        <td>Subject (CN):</td>
                                                        <td>{{ $certs['root']['subject']['CN'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Issuer (CN):</td>
                                                        <td>{{ $certs['root']['issuer']['CN'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Valid From:</td>
                                                        <td>{{ \Carbon\Carbon::parse($certs['root']['validFrom_time_t']) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Valid To:</td>
                                                        <td>{{ \Carbon\Carbon::parse($certs['root']['validTo_time_t']) }} <br> ({{ \Carbon\Carbon::parse($certs['root']['validTo_time_t'])->diffForHumans() }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Signature Type:</td>
                                                        <td>{{ $certs['root']['signatureTypeSN'] }}</td>
                                                    </tr>
                                                </table>
                                                @endif
                                            </td>
                                            <td>
                                                @if($certs['key'] != false)
                                                <table>
                                                    <tr>
                                                        <td>Bits:</td>
                                                        <td>{{ $certs['key']['bits'] }}</td>
                                                    </tr>
                                                    @if($server->server_cert_raw and $server->private_key_raw)
                                                    <tr>
                                                        <td>Verify Server-Cert/Key:</td>
                                                        <td>{{ (openssl_x509_check_private_key($server->server_cert_raw, $server->private_key_raw)) ? 'OK' : 'ERROR' }}</td>
                                                    </tr>
                                                    @endif
                                                </table>
                                                @endif
                                            </td>
                                        </tr>
                                        {{ Form::open(['route' => 'certificate.update']) }}
                                        {{ Form::hidden('customer_id', $server->customer->id) }}
                                        {{ Form::hidden('server_id', $server->id) }}
                                        <tr>
                                            <td>{{ Form::textarea('server', $server->server_cert_raw) }}</td>
                                            <td>{{ Form::textarea('intermediate', $server->customer->intermediate_cert_raw) }}</td>
                                            <td>{{ Form::textarea('root', $server->customer->root_cert_raw) }}</td>
                                            <td>{{ Form::textarea('private_key', $server->private_key_raw) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">{{ Form::submit('Submit') }}</td>
                                        </tr>
                                        {{ Form::close() }}
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
