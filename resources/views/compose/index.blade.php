@extends('layouts.app')
@section('title', 'Compose')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr>
                <td>
                    @if(auth()->user()->hasPermission('compose', 'editable'))
                        {!! Form::open(['route' => 'compose.upload', 'files' => true]) !!}
                        <div>
                            <strong>Upload Compose Files:</strong>
                        </div>
                        <div>
                            ZIP: {!! Form::file('compose_zip', ['accept' => '.zip']) !!}
                        </div>
                        <div>
                            YML: {!! Form::file('compose_files[]', ['multiple' => true, 'accept' => '.yml,.yaml']) !!}
                        </div>
                        <div>
                            {{ Form::submit('Upload') }}
                        </div>
                        {!! Form::close() !!}
                    @else
                        <strong>Upload Compose Files:</strong><br>
                        Keine Bearbeitungsberechtigung vorhanden.
                    @endif
                </td>
                @php($latestComposer = \App\Models\Composer::orderBy('updated_at', 'DESC')->first())
                <td>
                    Letztes Update:
                    @if($latestComposer)
                        {{ $latestComposer->updated_at->diffForHumans() }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <table id="pouetbox_prodmain">
            <tr id="prodheader">
                <th>Title</th>
                <th>Alternative Titles</th>
                <th>Date</th>
                <th>Container</th>
            </tr>
            @foreach($comp as $item)
                <tr>
                    <td><a href="{{ route('compose.show', $item->compose_filename) }}">{{ $item->title }}</a></td>
                    <td>{{ $item->title_alternatives }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->orig_date)->toDateString() }}</td>
                    <td>{{ \App\Models\ComposerContainerRel::whereComposerId($item->id)->count() }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
