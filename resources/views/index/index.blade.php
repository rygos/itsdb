@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr>
                    <th>ITS-DB Dashboard</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @include('index._partials.open_projects')
                    </td>
                </tr>
                <tr>
                    <td>
                        @include('index._partials.last5customers')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
