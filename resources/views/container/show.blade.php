@extends('layouts.app')
@section('title', 'Container: '.$data->title)
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr>
                <th>Container: {{ $data->title }}</th>
            </tr>
            <tr>
                <table id="pouetbox_prodmain">
                    <tr>
                        <td>Title:</td>
                        <td>{{ $data->title }}</td>
                    </tr>
                    <tr>
                        <td>Original Date:</td>
                        <td>{{ \Carbon\Carbon::parse($data->content_orig_date)->toDateString() }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Compose Data</th>
                    </tr>
                    {{ html()->form()->route('container.store', $data->title)->open() }}
                    <tr>
                        <td>Original</td>
                        <td>Customized</td>
                    </tr>
                    <tr>
                        <td style="text-align: left"><pre>{{ $data->content_orig }}</pre></td>
                        <td>{{ html()->textarea('content', $data->content)->attribute('rows', 16) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">{{ html()->submit('Submit') }}</td>
                    </tr>
                    {{ html()->form()->close() }}
                </table>
            </tr>
        </table>
    </div>
@endsection
