@extends('layouts.app')
@section('title', 'Compose')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr>
                <td>
                    @if(auth()->user()->hasPermission('compose', 'editable'))
                        {{ html()->form()->route('compose.upload')->attribute('enctype', 'multipart/form-data')->open() }}
                        <div>
                            <strong>Upload Compose Files:</strong>
                        </div>
                        <div>
                            ZIP: {{ html()->file('compose_zip')->attribute('accept', '.zip') }}
                        </div>
                        <div>
                            YML: {{ html()->file('compose_files[]')->attribute('multiple', true)->attribute('accept', '.yml,.yaml') }}
                        </div>
                        <div>
                            {{ html()->submit('Upload') }}
                        </div>
                        {{ html()->form()->close() }}
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
