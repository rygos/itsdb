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
        .product-matrix-alias-table select {
            min-width: 260px;
        }
        .product-matrix-alias-table td,
        .product-matrix-alias-table th {
            vertical-align: middle;
        }
        .product-matrix-alias-actions {
            white-space: nowrap;
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
                            @if(auth()->user()->hasPermission('product_matrix', 'editable'))
                                <form method="POST" action="{{ route('product_matrix.import') }}" enctype="multipart/form-data">
                                    @csrf
                                    <strong>Import:</strong>
                                    <input type="file" name="csv_file" accept=".csv,text/csv">
                                    <button type="submit">Import</button>
                                </form>
                            @endif

                            <form method="GET" action="{{ route('product_matrix.index') }}">
                                <strong>Suche:</strong>
                                {{ html()->text('search', $search)->attribute('placeholder', 'Produkt suchen') }}
                                {{ html()->submit('Filtern') }}
                            </form>
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
                    <th>Copy</th>
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
                        <td>
                            @if($entry->containers->isNotEmpty())
                                <button
                                    type="button"
                                    class="itsdb-copy-button"
                                    data-copy-value="{{ $entry->containers->pluck('title')->implode("\n") }}"
                                    data-copy-tooltip="Kopiert"
                                    title="Services kopieren"
                                >
                                    Copy
                                </button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Keine Produkte gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table id="pouetbox_prodmain" class="product-matrix-alias-table">
            <thead>
                <tr id="prodheader">
                    <th colspan="4">Spezifikations-Aliase fuer den Import</th>
                </tr>
                <tr id="prodheader">
                    <th>Alias aus CSV</th>
                    <th>Ziel-Container</th>
                    <th>Ignorieren</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @if(auth()->user()->hasPermission('product_matrix', 'editable'))
                    <tr>
                        <form method="POST" action="{{ route('product_matrix.aliases.store') }}">
                        @csrf
                        <td><input type="text" name="source_name" value="{{ old('source_name') }}" placeholder="z.B. user-provisioning"></td>
                        <td>
                            <select name="container_id">
                                <option value="">Bitte waehlen</option>
                                @foreach($containers as $container)
                                    <option value="{{ $container->id }}" @selected(old('container_id') == $container->id)>{{ $container->title }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="checkbox" name="ignore_on_import" value="1" @checked(old('ignore_on_import'))></td>
                        <td class="product-matrix-alias-actions"><button type="submit">Alias speichern</button></td>
                        </form>
                    </tr>
                @endif
                @forelse($aliases as $alias)
                    <tr>
                        @if(auth()->user()->hasPermission('product_matrix', 'editable'))
                            <form method="POST" action="{{ route('product_matrix.aliases.update', $alias->id) }}">
                            @csrf
                            <td><input type="text" name="source_name" value="{{ $alias->source_name }}"></td>
                            <td>
                                <select name="container_id">
                                    <option value="">Bitte waehlen</option>
                                    @foreach($containers as $container)
                                        <option value="{{ $container->id }}" @selected((string) $alias->container_id === (string) $container->id)>{{ $container->title }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="checkbox" name="ignore_on_import" value="1" @checked($alias->ignore_on_import)></td>
                            <td class="product-matrix-alias-actions">
                                <button type="submit">Speichern</button>
                                <a href="{{ route('product_matrix.aliases.delete', $alias->id) }}" class="itsdb-action-control" onclick="return confirm('Alias wirklich loeschen?')">Loeschen</a>
                            </td>
                            </form>
                        @else
                            <td>{{ $alias->source_name }}</td>
                            <td>{{ optional($containers->firstWhere('id', $alias->container_id))->title ?? '-' }}</td>
                            <td>{{ $alias->ignore_on_import ? 'Ja' : 'Nein' }}</td>
                            <td>-</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Noch keine Alias-Regeln vorhanden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
