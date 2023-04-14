@extends('layouts.app')
@section('title', 'Compose: '.$data->compose_filename)
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th>Compose File: {{ $data->compose_filename }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    {{ Form::open(['route' => ['compose.store', $data->compose_filename]]) }}
                    <table id="pouetbox_prodmain">
                        <tr>
                            <td>Title</td>
                            <td>{{ $data->title }}</td>
                        </tr>
                        <tr>
                            <td>Alternative Titles</td>
                            <td>{{ Form::textarea('title_alternatives', $data->title_alternatives, $attributes = ['rows' => 3]) }}</td>
                        </tr>
                        <tr>
                            <td>Original URL:</td>
                            <td><a href="{{ $data->orig_url }}" target="_blank">Download</a></td>
                        </tr>
                        <tr>
                            <td>Compose Date</td>
                            <td>{{ \Carbon\Carbon::parse($data->orig_date)->toDateString() }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ Form::submit('Submit') }}</td>
                        </tr>
                    </table>
                    {{ Form::close() }}
                </td>
            </tr>
            <tr>
                <td>
                    <table id="pouetbox_prodmain">
                        <tr>
                            <th>Container</th>
                            <th>Original Date</th>
                        </tr>
                        @foreach($data->rel()->get() as $item)
                            <tr>
                                <td><a href="{{ route('container.show', $item->container->title) }}">{{ $item->container->title }}</a></td>
                                <td>{{ \Carbon\Carbon::parse($item->container->content_orig_date)->toDateString() }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
