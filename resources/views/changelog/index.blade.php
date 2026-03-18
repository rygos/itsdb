@extends('layouts.app')
@section('title', 'Changelog')
@section('content')

    <div id="prodpagecontainer">
        @if($editor == 1)
            {{ html()->form()->route('changelog.add_version')->open() }}
            <div class="pouettbl" id="pouetbox_version_add">
                <h2>Add Version</h2>
                <div class="content">
                    <table>
                        <tr>
                            <td>Version:</td>
                            <td>{{ html()->text('version') }}</td>
                        </tr>
                        <tr>
                            <td>Description (Optional)</td>
                            <td>{{ html()->text('description') }}</td>
                        </tr>
                        <tr><td></td><td>{{ html()->submit('Submit') }}</td></tr>
                    </table>
                </div>
            </div>
            {{ html()->form()->close() }}
        @endif

        @foreach($versions as $v)
            <div class="pouettbl" id="pouetbox_repair_{{ $v->id }}">
                {{ html()->form()->route('changelog.publish_version')->open() }}
                {{ html()->hidden('version_id', $v->id) }}
                <h2>{{ \Carbon\Carbon::parse($v->published_at)->format('d.m.Y') }} - {{ $v->version }}@if($v->description) {{ ' - '.$v->description }} @endif @if($editor == 1 and $v->published == 0) {{ html()->submit('Publish') }} @endif</h2>
                {{ html()->form()->close() }}
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
                                {{ html()->form()->route('changelog.add_changelog')->open() }}
                                {{ html()->hidden('version_id', $v->id) }}
                                <td>
                                    {{ html()->text('type') }}
                                </td>
                                <td>
                                    {{ html()->text('description') }} {{ html()->submit('Submit') }}
                                </td>
                                {{ html()->form()->close() }}
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endforeach
    </div>

@endsection
