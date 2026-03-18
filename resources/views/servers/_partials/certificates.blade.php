<tr>
    <th>Certificates</th>
</tr>
<tr>
    <td>
        <table style="width: 100%">
            <tr>
                <td colspan="4">
                    @if($errors->has('pfx_file'))
                        <div style="margin-bottom: 10px; color: #ff8080;">{{ $errors->first('pfx_file') }}</div>
                    @endif
                    {{ html()->form()->route('certificate.import_pfx')->attribute('enctype', 'multipart/form-data')->open() }}
                    {{ html()->hidden('customer_id', $server->customer->id) }}
                    {{ html()->hidden('server_id', $server->id) }}
                    <table style="width: 100%">
                        <tr>
                            <th>PFX-Datei</th>
                            <th>PFX-Passwort</th>
                            <th>Aktion</th>
                        </tr>
                        <tr>
                            <td>{{ html()->file('pfx_file') }}</td>
                            <td>{{ html()->text('pfx_password') }}</td>
                            <td>{{ html()->submit('Upload') }}</td>
                        </tr>
                    </table>
                    {{ html()->form()->close() }}
                </td>
            </tr>
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
            {{ html()->form()->route('certificate.update')->open() }}
            {{ html()->hidden('customer_id', $server->customer->id) }}
            {{ html()->hidden('server_id', $server->id) }}
            <tr>
                <td>{{ html()->textarea('server', $server->server_cert_raw) }}</td>
                <td>{{ html()->textarea('intermediate', $server->customer->intermediate_cert_raw) }}</td>
                <td>{{ html()->textarea('root', $server->customer->root_cert_raw) }}</td>
                <td>{{ html()->textarea('private_key', $server->private_key_raw) }}</td>
            </tr>
            <tr>
                <td colspan="4">
                    {{ html()->submit('Submit') }}
                    <button
                        type="button"
                        class="itsdb-copy-button"
                        data-copy-tooltip="Kopiert"
                        data-copy-from-fields='["textarea[name=\"server\"]","textarea[name=\"intermediate\"]","textarea[name=\"root\"]","textarea[name=\"private_key\"]"]'
                        title="Zertifikate im Linux-Format kopieren"
                    >
                        Linux PEM kopieren
                    </button>
                </td>
            </tr>
            {{ html()->form()->close() }}
        </table>
    </td>
</tr>
