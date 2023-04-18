@extends('layouts.app')
@section('title', 'Changelog')
@section('content')

    <div id="prodpagecontainer">
        @if($editor == 1)
            {{ Form::open(['method' => 'POST', 'route' => 'changelog.add_version']) }}
            <div class="pouettbl" id="pouetbox_version_add">
                <h2>Add Version</h2>
                <div class="content">
                    <table>
                        <tr>
                            <td>Version:</td>
                            <td>{{ Form::text('version') }}</td>
                        </tr>
                        <tr>
                            <td>Description (Optional)</td>
                            <td>{{ Form::text('description') }}</td>
                        </tr>
                        <tr><td></td><td>{{ Form::submit() }}</td></tr>
                    </table>
                </div>
            </div>
            {{ Form::close() }}
        @endif

        @foreach($versions as $v)
            <div class="pouettbl" id="pouetbox_repair_{{ $v->id }}">
                {{ Form::open(['method' => 'POST', 'route' => 'changelog.publish_version']) }}
                {{ Form::hidden('version_id', $v->id) }}
                <h2>{{ \Carbon\Carbon::parse($v->published_at)->format('d.m.Y') }} - {{ $v->version }}@if($v->description) {{ ' - '.$v->description }} @endif @if($editor == 1 and $v->published == 0) {{ Form::submit('Publish') }} @endif</h2>
                {{ Form::close() }}
                <div class="content">
                    <table>
                        @foreach(\App\Models\Changelog::where('version_id', '=', $v->id)->get() as $item)
                            <tr>
                                <td>{{ $item->type }}</td>
                                <td>{{ $item->description }}</td>
                            </tr>
                        @endforeach
                        @if($editor == 1)
                            <tr>
                                {{ Form::open(['method' => 'POST', 'route' => 'changelog.add_changelog']) }}
                                {{ Form::hidden('version_id', $v->id) }}
                                <td>
                                    {{ Form::text('type') }}
                                </td>
                                <td>
                                    {{ Form::text('description') }} {{ Form::submit() }}
                                </td>
                                {{ Form::close() }}
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endforeach
    </div>

@endsection
