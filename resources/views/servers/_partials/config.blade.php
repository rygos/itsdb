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
                        {{ html()->form()->route('servers.add_composer', $server->id)->open() }}
                        <tr>
                            <th>Docker-Compose</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <td>Add docker-compose: {{ html()->select('compose', $compose_select) }}</td>
                            <td>{{ html()->submit('Submit') }}</td>
                        </tr>
                        @foreach($compose as $item)
                            <tr>
                                <td><a href="{{ route('compose.show', $item->composer->compose_filename) }}">{{ $item->composer->title }} @if(!is_null($item->composer->title_alternatives)) {{ ' ('.$item->composer->title_alternatives.')' }} @endif</a></td>
                                <td><a href="{{ route('servers.del_composer', [$server->id, $item->composer_id]) }}" class="itsdb-action-control">delete</a></td>
                            </tr>
                        @endforeach
                        {{ html()->form()->close() }}
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
                        {{ html()->form()->route('env.update', $server->id)->open() }}
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
                                <td>{{ html()->text($item->key, $item->value) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2">{{ html()->submit('Submit') }}<br><br><a href="{{ route('env.generate_raw', $server->id) }}">Generate .env Textfield (Overwrites Content)</a></td>
                        </tr>
                        {{ html()->form()->close() }}
                    </table>
                </td>
            </tr>
            {{ html()->form()->route('servers.update_serverconfig')->open() }}
            {{ html()->hidden('server_id', $server->id) }}
            <tr>
                <td>
                    {{ html()->textarea('docker_compose', $server->docker_compose_raw)->attribute('cols', 100) }}
                </td>
                <td>
                    {{ html()->textarea('env', $server->env_raw)->attribute('cols', 100) }}
                </td>
            </tr>
            <tr>
                <td><a href="{{ route('compose.generate', $server->id) }}">Generate docker-compose.yml</a></td>
                <td><a href="{{ route('env.generate_from_raw', $server->id) }}">Generate .env config from Textfield (Overwrites Informations)</a></td>
            </tr>
            <tr>
                <td colspan="2">{{ html()->submit('Submit') }}</td>
            </tr>
            {{ html()->form()->close() }}
        </table>
    </td>
</tr>
