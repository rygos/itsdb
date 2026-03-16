@extends('layouts.app')
@section('title', 'Produkte Matrix')
@section('content')
    <style>
        .product-matrix-toolbar {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .product-matrix-toolbar form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .product-matrix-status {
            margin: 8px 0;
            padding: 6px 8px;
            text-align: left;
        }
        .product-matrix-status.success {
            background-color: #214d21;
        }
        .product-matrix-status.error {
            background-color: #6b1d1d;
        }
        .product-matrix-specs {
            text-align: left;
        }
        .product-matrix-specs ul {
            margin-left: 18px;
        }
    </style>
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th colspan="2">
                        <span id="title"><big>Produkte Matrix</big></span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="product-matrix-toolbar">
                            {!! Form::open(['route' => 'product_matrix.import', 'files' => true]) !!}
                                <strong>Import:</strong>
                                {!! Form::file('csv_file', ['accept' => '.csv,text/csv']) !!}
                                {{ Form::submit('Import') }}
                            {!! Form::close() !!}

                            {!! Form::open(['route' => 'product_matrix.index', 'method' => 'get']) !!}
                                <strong>Suche:</strong>
                                {!! Form::text('search', $search, ['placeholder' => 'Produkt suchen']) !!}
                                {{ Form::submit('Filtern') }}
                            {!! Form::close() !!}
                        </div>

                        @if(session('status'))
                            <div class="product-matrix-status success">{{ session('status') }}</div>
                        @endif

                        @if(isset($errors) && $errors->any())
                            <div class="product-matrix-status error">{{ $errors->first() }}</div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <table id="pouetbox_prodmain" data-sortable="true">
            <thead>
                <tr id="prodheader">
                    <th>Kategorie</th>
                    <th>Funktion</th>
                    <th>Produkt</th>
                    <th>Kurzbeschreibung</th>
                    <th>Synonyme</th>
                    <th>Beschreibung</th>
                    <th>Orbis U Spezifikation</th>
                </tr>
            </thead>
            <tbody id="pouetbox_prodlist">
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry->category }}</td>
                        <td>{{ $entry->function_name }}</td>
                        <td>{{ $entry->product }}</td>
                        <td>{{ $entry->short_description }}</td>
                        <td>{{ $entry->synonyms }}</td>
                        <td>{!! nl2br(e($entry->description)) !!}</td>
                        <td class="product-matrix-specs">
                            @if($entry->containers->isNotEmpty())
                                <ul>
                                    @foreach($entry->containers as $container)
                                        <li>
                                            <a href="{{ route('container.show', $container->title) }}">{{ $container->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Keine Produkte gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
