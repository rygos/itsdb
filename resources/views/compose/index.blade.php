@extends('layouts.app')
@section('title', 'Compose')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <tr>
                <td><a href="{{ route('compose.update') }}">Update Composer Templates (Dauert ein paar Sekunden)</a></td>
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
