@extends('layouts.app')
@section('title', 'Register User')
@section('content')
    <div id="prodpagecontainer">
        {{ Form::open(['route' => 'register']) }}
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th colspan="2">
                    <span id="title"><big>Register User</big></span>
                </th>
            </tr>
            <tr>
                <td>Name</td>
                <td>{{ Form::text('name') }}</td>
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
                <td>Confirm Password</td>
                <td>{{ Form::password('password_confirmation') }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ Form::submit('Submit') }}</td>
            </tr>
        </table>
        {{ Form::close() }}
    </div>
@endsection
