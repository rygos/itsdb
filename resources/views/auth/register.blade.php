@extends('layouts.app')
@section('title', 'Register User')
@section('content')
    <div id="prodpagecontainer">
        {{ html()->form()->route('register')->open() }}
        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th colspan="2">
                    <span id="title"><big>Register User</big></span>
                </th>
            </tr>
            <tr>
                <td>Name</td>
                <td>{{ html()->text('name') }}</td>
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
                <td>Confirm Password</td>
                <td>{{ html()->password('password_confirmation') }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ html()->submit('Submit') }}</td>
            </tr>
        </table>
        {{ html()->form()->close() }}
    </div>
@endsection
