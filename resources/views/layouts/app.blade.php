<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>ITS-DB - @yield('title')</title>

    <link rel="stylesheet" type="text/css" href="/css/app.css"
          media="screen"/>

    <meta property="og:type" content="website"/>

    <script type="text/javascript">
        <!--
        var pixelWidth = screen.width;
        var Pouet = {};
        Pouet.isMobile = false;
        //-->
    </script>
    <script src="/js/app.js"></script>
    @unless(request()->routeIs('customers_projects.add'))
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js"></script>
    @endunless
    <script   src="https://code.jquery.com/jquery-1.9.1.min.js"   integrity="sha256-wS9gmOZBqsqWxgIVgA8Y9WcQOa7PgSIX+rPA0VL2rbQ="   crossorigin="anonymous"></script>

    <!--[if lt IE 9]>
    <script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->
    <!--[if IE]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

    @if(Auth::id() == 2)
        <style>
            body, a:hover, textarea, input {
                cursor: url(http://cur.cursors-4u.net/cursors/cur-2/cur223.cur), default !important;
            }
        </style>
    @endif

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<header>
    <h1>ITS-DB</h1>
</header>
<nav id="topbar">
    @include('_partials.navigation')
</nav>
<div id='content'>
    @yield('content')
</div>
<nav id="bottombar">
    @include('_partials.navigation')
</nav>
<footer>
    <ul>
        <li>Made with Love and Beer</li>
        <li>{{ config('app.name') }} - <a href="{{ route('changelog.index') }}">
                {{  @\App\Models\ChangelogVersion::latest()->where('published', '=', 1)->first()->version }}
            </a></li>
    </ul>
</footer>
@if(isset($term))
    <script>
        $(document).ready(function() {
            var src_str = $("#pouetbox_prodlist").html();
            var term = "{{ $term }}";
            term = term.replace(/(\s+)/,"(<[^>]+>)*$1(<[^>]+>)*");
            var pattern = new RegExp("("+term+")", "gi");

            src_str = src_str.replace(pattern, "<mark>$1</mark>");
            src_str = src_str.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/,"$1</mark>$2<mark>$4");

            $("#pouetbox_prodlist").html(src_str);
        });
    </script>
@endif
@unless(request()->routeIs('customers_projects.add'))
    <script>
        (function() {
            function parseValue(text) {
                var trimmed = (text || '').trim();
                if (trimmed === '') return { type: 'text', value: '' };

                var dateMatch = trimmed.match(/^(\d{4}-\d{2}-\d{2})/);
                if (dateMatch) {
                    var dateValue = new Date(dateMatch[1] + 'T00:00:00');
                    if (!isNaN(dateValue.getTime())) {
                        return { type: 'date', value: dateValue.getTime() };
                    }
                }

                var numberMatch = trimmed.replace(',', '.').match(/^-?\d+(\.\d+)?$/);
                if (numberMatch) {
                    return { type: 'number', value: parseFloat(numberMatch[0]) };
                }

                return { type: 'text', value: trimmed.toLowerCase() };
            }

            function sortTable(table, columnIndex, direction) {
                var tbody = table.tBodies[0];
                if (!tbody) return;

                var rows = Array.prototype.slice.call(tbody.rows);
                var headerCount = table.tHead ? table.tHead.rows[0].cells.length : (table.rows[0] ? table.rows[0].cells.length : 0);
                var sortableRows = rows.filter(function(row) {
                    return row.cells.length === headerCount;
                });
                var staticRows = rows.filter(function(row) {
                    return row.cells.length !== headerCount;
                });

                sortableRows.sort(function(a, b) {
                    var aText = a.cells[columnIndex] ? a.cells[columnIndex].innerText : '';
                    var bText = b.cells[columnIndex] ? b.cells[columnIndex].innerText : '';
                    var aParsed = parseValue(aText);
                    var bParsed = parseValue(bText);

                    var aVal = aParsed.value;
                    var bVal = bParsed.value;
                    if (aParsed.type !== bParsed.type) {
                        aVal = (aText || '').toLowerCase();
                        bVal = (bText || '').toLowerCase();
                    }

                    if (aVal < bVal) return direction === 'asc' ? -1 : 1;
                    if (aVal > bVal) return direction === 'asc' ? 1 : -1;
                    return 0;
                });

                tbody.innerHTML = '';
                staticRows.forEach(function(row) { tbody.appendChild(row); });
                sortableRows.forEach(function(row) { tbody.appendChild(row); });
            }

            function initSortableTables() {
                var tables = document.querySelectorAll('table');
                tables.forEach(function(table) {
                    var header = table.tHead ? table.tHead.rows[0] : table.rows[0];
                    if (!header) return;
                    var cells = Array.prototype.slice.call(header.cells);
                    if (!cells.length) return;

                    cells.forEach(function(cell, index) {
                        cell.style.cursor = 'pointer';
                        cell.addEventListener('click', function() {
                            var current = cell.getAttribute('data-sort-dir');
                            var next = current === 'asc' ? 'desc' : 'asc';
                            cells.forEach(function(other) {
                                if (other !== cell) other.removeAttribute('data-sort-dir');
                            });
                            cell.setAttribute('data-sort-dir', next);
                            sortTable(table, index, next);
                        });
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSortableTables);
            } else {
                initSortableTables();
            }
        })();
    </script>
@endunless
</body>
</html>
