@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div id="prodpagecontainer">
        {{ Form::open(['route' => 'login']) }}
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th colspan="2">
                    <span id="title"><big>Login User</big></span>
                </th>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td>{{ Form::email('email') }}</td>
            </tr>
            <tr>
                <td>Password</td>
                <td>{{ Form::password('password') }}</td>
            </tr>
            <tr>
                <td>Remember Me</td>
                <td>{{ Form::checkbox('remember_me') }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ Form::submit('Submit') }}</td>
            </tr>
        </table>
        {{ Form::close() }}
    </div>
@endsection
