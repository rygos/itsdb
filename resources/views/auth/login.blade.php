@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div id="prodpagecontainer">
        {{ html()->form()->route('login')->open() }}
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th colspan="2">
                    <span id="title"><big>Login User</big></span>
                </th>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td>{{ html()->email('email') }}</td>
            </tr>
            <tr>
                <td>Password</td>
                <td>{{ html()->password('password') }}</td>
            </tr>
            <tr>
                <td>Remember Me</td>
                <td>{{ html()->checkbox('remember_me') }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ html()->submit('Submit') }}</td>
            </tr>
            @if(config('app.registration_enabled'))
                <tr>
                    <td colspan="2"><a href="{{ route('register') }}">Register new user</a></td>
                </tr>
            @endif
        </table>
        {{ html()->form()->close() }}
    </div>
@endsection
