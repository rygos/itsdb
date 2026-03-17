@extends('layouts.app')
@section('title', 'Benutzer bearbeiten')
@section('content')
    <div id="prodpagecontainer" class="admin-page">
        {{ Form::open(['route' => ['administration.users.update', $editUser]]) }}
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th colspan="2">
                        <span id="title"><big>Benutzer bearbeiten</big></span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @if($errors->any())
                    <tr>
                        <td colspan="2" class="admin-error-message">{{ $errors->first() }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Benutzername</td>
                    <td>{!! Form::text('name', old('name', $editUser->name)) !!}</td>
                </tr>
                <tr>
                    <td>Email Adresse</td>
                    <td>{!! Form::email('email', old('email', $editUser->email)) !!}</td>
                </tr>
                <tr>
                    <td>Passwort</td>
                    <td>{!! Form::password('password') !!}</td>
                </tr>
                <tr>
                    <td>Passwort bestaetigen</td>
                    <td>{!! Form::password('password_confirmation') !!}</td>
                </tr>
            </tbody>
        </table>

        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th>Bereich</th>
                    <th>Sichtbar</th>
                    <th>Editierbar</th>
                    <th>Administration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissionAreas as $areaKey => $areaLabel)
                    @php($currentLevel = old("permissions.$areaKey") ? \App\Models\User::resolvePermissionLevel(old("permissions.$areaKey")) : $editUser->permissionLevel($areaKey))
                    <tr>
                        <td>{{ $areaLabel }}</td>
                        @foreach($permissionLevels as $levelValue => $levelLabel)
                            <td>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="permissions[{{ $areaKey }}][]"
                                        value="{{ $levelValue }}"
                                        @checked($currentLevel >= $levelValue)
                                    >
                                    {{ $levelLabel }}
                                </label>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">
                        {{ Form::submit('Benutzer speichern') }}
                        <a href="{{ route('administration.index', ['tab' => 'users']) }}">Zurueck</a>
                    </td>
                </tr>
            </tbody>
        </table>
        {{ Form::close() }}
    </div>
@endsection
