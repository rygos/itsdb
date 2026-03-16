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
    @php($latestPublishedVersion = \App\Models\ChangelogVersion::query()->where('published', 1)->latest()->first())
    <ul>
        <li>Made with Love and Beer</li>
        <li>{{ config('app.name') }} - <a href="{{ route('changelog.index') }}">
                {{ $latestPublishedVersion?->version ?? '-' }}
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
                var tables = document.querySelectorAll('table[data-sortable="true"], table thead');
                var processedTables = [];
                tables.forEach(function(entry) {
                    var table = entry.tagName === 'TABLE' ? entry : entry.closest('table');
                    if (!table || processedTables.indexOf(table) !== -1) return;
                    processedTables.push(table);

                    var header = table.tHead ? table.tHead.rows[0] : null;
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
<script>
    (function() {
        function fallbackCopyText(value) {
            var element = document.createElement('textarea');
            element.value = value;
            element.setAttribute('readonly', 'readonly');
            element.style.position = 'absolute';
            element.style.left = '-9999px';
            document.body.appendChild(element);
            element.select();
            document.execCommand('copy');
            document.body.removeChild(element);
        }

        function copyText(value) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(value);
            }

            fallbackCopyText(value);
            return Promise.resolve();
        }

        function flashCopyState(button) {
            if (!button) return;

            var originalTitle = button.getAttribute('data-original-title') || button.getAttribute('title') || '';
            if (!button.getAttribute('data-original-title')) {
                button.setAttribute('data-original-title', originalTitle);
            }

            button.setAttribute('title', 'Kopiert');
            button.classList.add('is-copied');
            button.classList.add('show-copy-tooltip');

            window.setTimeout(function() {
                button.setAttribute('title', originalTitle);
                button.classList.remove('is-copied');
                button.classList.remove('show-copy-tooltip');
            }, 1200);
        }

        function setSecretVisibility(field, isVisible) {
            if (!field) return;

            var text = field.querySelector('[data-secret-text]');
            var toggle = field.querySelector('[data-secret-toggle]');
            var copyButton = field.querySelector('[data-copy-value]');
            if (!text || !toggle || !copyButton) return;

            var secretValue = copyButton.getAttribute('data-copy-value') || '';
            var hiddenText = text.getAttribute('data-hidden-text') || '-hidden-';

            text.textContent = isVisible ? secretValue : hiddenText;
            text.setAttribute('data-visible', isVisible ? 'true' : 'false');
            toggle.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
            toggle.setAttribute('title', isVisible ? 'Passwort ausblenden' : 'Passwort anzeigen');
            toggle.classList.toggle('is-visible', isVisible);
        }

        function initCredentialClipboard() {
            document.querySelectorAll('[data-copy-value]').forEach(function(button) {
                button.addEventListener('click', function() {
                    copyText(button.getAttribute('data-copy-value') || '')
                        .then(function() {
                            flashCopyState(button);
                        });
                });
            });

            document.querySelectorAll('[data-copy-from-fields]').forEach(function(button) {
                button.addEventListener('click', function() {
                    var selectors = JSON.parse(button.getAttribute('data-copy-from-fields') || '[]');
                    var values = selectors.map(function(selector) {
                        var field = document.querySelector(selector);
                        return field ? (field.value || '').trim() : '';
                    }).filter(function(value) {
                        return value !== '';
                    });

                    copyText(values.join("\n"))
                        .then(function() {
                            flashCopyState(button);
                        });
                });
            });

            document.querySelectorAll('[data-secret-toggle]').forEach(function(toggle) {
                var field = toggle.closest('[data-secret-field]');
                setSecretVisibility(field, false);

                toggle.addEventListener('click', function() {
                    var isVisible = toggle.getAttribute('aria-pressed') === 'true';
                    setSecretVisibility(field, !isVisible);
                });
            });
        }

        function setModalState(modal, isOpen) {
            if (!modal) return;
            modal.style.display = isOpen ? 'flex' : 'none';
            modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        }

        function initModals() {
            document.querySelectorAll('[data-modal-target]').forEach(function(trigger) {
                trigger.addEventListener('click', function() {
                    setModalState(document.querySelector(trigger.getAttribute('data-modal-target')), true);
                });
            });

            document.querySelectorAll('[data-modal-close]').forEach(function(trigger) {
                trigger.addEventListener('click', function() {
                    setModalState(trigger.closest('.itsdb-modal'), false);
                });
            });

            document.querySelectorAll('.itsdb-modal').forEach(function(modal) {
                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        setModalState(modal, false);
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Escape') return;
                document.querySelectorAll('.itsdb-modal[aria-hidden="false"]').forEach(function(modal) {
                    setModalState(modal, false);
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initCredentialClipboard();
                initModals();
            });
        } else {
            initCredentialClipboard();
            initModals();
        }
    })();
</script>
@yield('scripts')
</body>
</html>
