@extends('layouts.app')
@section('title', 'Serverinfo')
@section('content')
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
                <tr id="prodheader">
                    <th colspan='1'>
                        <span id='title'><big><a href="{{ route('customers.view', $server->customer->id) }}">{{ $server->customer->short_no }} - {{ $server->customer->sap_no }} - {{ $server->customer->name }}</a></big></span>
                        <div id='nfo'>{{ $server->servername }}</div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table style="width: 100%">
                            <tr>
                                <th>Informations</th>
                            </tr>
                            <tr>
                                <td>
                                    {{ html()->form()->route('servers.update')->open() }}
                                    {{ html()->hidden('server_id', $server->id) }}
                                    <table style="width: 100%">
                                        <tr>
                                            <th>Type</th>
                                            <th>Serverart</th>
                                            <th>OS</th>
                                            <th>Servername</th>
                                            <th>FQDN</th>
                                            <th>DB-SID</th>
                                            <th>DB-Server</th>
                                            <th>ext. IP</th>
                                            <th>int. IP</th>
                                            <th>Certificate</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td>{{ html()->select('type', ['' => '', 'Produktiv' => 'Produktiv', 'Test' => 'Test', 'Schulungs' => 'Schulungs', 'Entwicklungs' => 'Entwicklungs', 'Integration' => 'Integration', 'Auswerte' => 'Auswerte'], $server->type) }}</td>
                                            <td>{{ html()->select('server_kind_id', $serverKindOptions, $server->server_kind_id) }}</td>
                                            <td>{{ html()->select('operating_system_id', $operatingSystemOptions, $server->operating_system_id) }}</td>
                                            <td>{{ html()->text('servername', $server->servername) }}</td>
                                            <td>{{ html()->text('fqdn', $server->fqdn) }}</td>
                                            <td>{{ html()->text('db_sid', $server->db_sid) }}</td>
                                            <td>{{ html()->text('db_server', $server->db_server) }}</td>
                                            <td>{{ html()->text('ext_ip', $server->ext_ip) }}</td>
                                            <td>{{ html()->text('int_ip', $server->int_ip) }}</td>
                                            <td></td>
                                            <td>{{ html()->submit('Submit') }}</td>
                                        </tr>
                                    </table>
                                    {{ html()->form()->close() }}
                                </td>
                            </tr>
                            <tr>
                                <th>Zugeordnete Credentials</th>
                            </tr>
                            <tr>
                                <td>
                                    <table style="width: 100%">
                                        <tr>
                                            <th>User</th>
                                            <th>Pass</th>
                                            <th>Type</th>
                                            <th>Server</th>
                                            <th>Erstellt</th>
                                            <th>Aktion</th>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <button type="button" data-modal-target="#server-credential-create-modal">Credential fuer diesen Server hinzufuegen</button>
                                            </td>
                                        </tr>
                                        @forelse($credentials as $item)
                                            <tr>
                                                <td>
                                                    @include('_partials.credential-copy-field', [
                                                        'copyValue' => $item->username,
                                                    ])
                                                </td>
                                                <td>
                                                    @include('_partials.credential-copy-field', [
                                                        'copyValue' => $item->password,
                                                        'isPassword' => true,
                                                    ])
                                                </td>
                                                <td>{{ $item->type }}</td>
                                                <td>{{ $item->servers->pluck('servername')->implode(', ') }}</td>
                                                <td>{{ $item->created_at }}</td>
                                                <td>
                                                    <div class="itsdb-actions">
                                                        <button type="button" data-modal-target="#server-credential-edit-modal-{{ $item->id }}">bearbeiten</button>
                                                        <a href="{{ route('credentials.delete', $item->id) }}" class="itsdb-action-control">delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">Keine Credentials mit diesem Server verknuepft.</td>
                                            </tr>
                                        @endforelse
                                    </table>
                                </td>
                            </tr>
                            @include('servers._partials.config')
                            @include('servers._partials.certificates')
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="itsdb-modal" id="server-credential-create-modal" aria-hidden="true">
        <div class="itsdb-modal__dialog">
            <div class="itsdb-modal__header">
                <div class="itsdb-modal__title">Credential fuer {{ $server->servername }} hinzufuegen</div>
                <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
            </div>
            <div class="itsdb-modal__body">
                {{ html()->form()->route('credentials.store')->open() }}
                {{ html()->hidden('customer_id', $server->customer->id) }}
                <table class="itsdb-modal__grid">
                    <tr>
                        <td class="itsdb-modal__grid-label">User</td>
                        <td>{{ html()->text('username') }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Passwort</td>
                        <td>{{ html()->text('password') }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Typ</td>
                        <td>{{ html()->select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'Orbis Auth' => 'Orbis Auth', 'OAS' => 'OAS', 'OAS Admin' => 'OAS Admin', 'PTC-Share' => 'PTC-Share']) }}</td>
                    </tr>
                    <tr>
                        <td class="itsdb-modal__grid-label">Server</td>
                        <td>
                            <div class="itsdb-server-picker">
                                @foreach($server->customer->servers as $serverOption)
                                    <label class="itsdb-server-picker__option">
                                        <input type="checkbox" name="server_ids[]" value="{{ $serverOption->id }}" @checked((string) $serverOption->id === (string) $server->id)>
                                        <span>{{ $serverOption->servername }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="itsdb-modal__footer">
                    {{ html()->submit('Speichern') }}
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>

    @foreach($credentials as $item)
        <div class="itsdb-modal" id="server-credential-edit-modal-{{ $item->id }}" aria-hidden="true">
            <div class="itsdb-modal__dialog">
                <div class="itsdb-modal__header">
                    <div class="itsdb-modal__title">Credential bearbeiten</div>
                    <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
                </div>
                <div class="itsdb-modal__body">
                    {{ html()->form()->route('credentials.update')->open() }}
                    {{ html()->hidden('id', $item->id) }}
                    <table class="itsdb-modal__grid">
                        <tr>
                            <td class="itsdb-modal__grid-label">User</td>
                            <td>{{ html()->text('username', $item->username) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Passwort</td>
                            <td>{{ html()->text('password', $item->password) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Typ</td>
                            <td>{{ html()->select('type', ['Windows Misc' => 'Windows Misc', 'OrbisU' => 'OrbisU', 'Orbis User' => 'Orbis User', 'Orbis Auth' => 'Orbis Auth', 'OAS' => 'OAS', 'OAS Admin' => 'OAS Admin', 'PTC-Share' => 'PTC-Share'], $item->type) }}</td>
                        </tr>
                        <tr>
                            <td class="itsdb-modal__grid-label">Server</td>
                            <td>
                                @php($selectedServerIds = $item->servers->pluck('id')->map(fn ($id) => (string) $id)->all())
                                <div class="itsdb-server-picker">
                                    @foreach($server->customer->servers as $serverOption)
                                        <label class="itsdb-server-picker__option">
                                            <input type="checkbox" name="server_ids[]" value="{{ $serverOption->id }}" @checked(in_array((string) $serverOption->id, $selectedServerIds, true))>
                                            <span>{{ $serverOption->servername }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="itsdb-modal__footer">
                        {{ html()->submit('Aktualisieren') }}
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script>
        (function() {
            function initServerComposeWorkspace() {
            var root = document.querySelector('[data-compose-workspace]');
            if (!root || root.getAttribute('data-compose-workspace-initialized') === 'true') {
                return;
            }

            root.setAttribute('data-compose-workspace-initialized', 'true');

            var dataElement = root.querySelector('[data-compose-workspace-json]');
            if (!dataElement) {
                return;
            }

            var data = {};
            try {
                data = JSON.parse(dataElement.textContent || '{}');
            } catch (error) {
                console.error('Compose workspace data could not be parsed.', error);
                return;
            }

            var containers = data.containers || [];
            var products = data.products || [];
            var containerMap = {};
            var productMap = {};
            var containerTitleMap = {};
            var selectedProductIds = new Set();
            var selectedContainerIds = new Set();
            var baselineContainerIds = new Set();
            var unknownServices = [];
            var composeInput = root.querySelector('[data-compose-input]');
            var diffOutput = root.querySelector('[data-compose-diff-output]');
            var diffCopyButton = root.querySelector('[data-compose-diff-copy]');
            var productSearch = root.querySelector('[data-product-search]');
            var containerSearch = root.querySelector('[data-container-search]');
            var analyzeTimer = null;

            containers.forEach(function(container) {
                containerMap[container.id] = container;
                containerTitleMap[normalizeKey(container.title)] = container;
            });

            products.forEach(function(product) {
                productMap[product.id] = product;
            });

            function normalizeKey(value) {
                return (value || '')
                    .trim()
                    .replace(/^['"]|['"]$/g, '')
                    .toLowerCase();
            }

            function parseComposeServices(text) {
                var services = [];
                var lines = (text || '').split(/\r\n|\n|\r/);
                var inServices = false;
                var servicesIndent = 0;

                lines.forEach(function(line) {
                    var trimmed = (line || '').trim();
                    if (trimmed === '' || trimmed.indexOf('#') === 0) {
                        return;
                    }

                    var indent = line.length - line.replace(/^\s+/, '').length;

                    if (!inServices && trimmed === 'services:') {
                        inServices = true;
                        servicesIndent = indent;
                        return;
                    }

                    if (!inServices) {
                        return;
                    }

                    if (indent <= servicesIndent) {
                        inServices = false;
                        return;
                    }

                    if (indent === servicesIndent + 2) {
                        var match = trimmed.match(/^['"]?([A-Za-z0-9._-]+)['"]?:\s*$/);
                        if (match) {
                            services.push(match[1]);
                        }
                    }
                });

                return services;
            }

            function createChip(label, variant) {
                var chip = document.createElement('span');
                chip.className = 'server-compose-chip';
                if (variant) {
                    chip.classList.add('server-compose-chip--' + variant);
                }
                chip.textContent = label;

                return chip;
            }

            function renderChipList(target, labels, variant, emptyText) {
                target.innerHTML = '';

                if (!labels.length) {
                    target.appendChild(createChip(emptyText || 'Keine Eintraege', 'muted'));
                    return;
                }

                labels.forEach(function(label) {
                    target.appendChild(createChip(label, variant));
                });
            }

            function getTargetContainerIds() {
                var targetIds = new Set(Array.from(baselineContainerIds));

                selectedContainerIds.forEach(function(containerId) {
                    targetIds.add(containerId);
                });

                selectedProductIds.forEach(function(productId) {
                    var product = productMap[productId];
                    if (!product) {
                        return;
                    }

                    (product.container_ids || []).forEach(function(containerId) {
                        targetIds.add(containerId);
                    });
                });

                return targetIds;
            }

            function getAddedContainerIds(targetIds) {
                return Array.from(targetIds).filter(function(containerId) {
                    return !baselineContainerIds.has(containerId);
                });
            }

            function getCoveredProducts(targetIds, requireFullCoverage) {
                return products
                    .map(function(product) {
                        var covered = (product.container_ids || []).filter(function(containerId) {
                            return targetIds.has(containerId);
                        }).length;

                        return {
                            id: product.id,
                            label: product.label,
                            covered: covered,
                            total: (product.container_ids || []).length,
                        };
                    })
                    .filter(function(product) {
                        if (product.total === 0) {
                            return false;
                        }

                        if (requireFullCoverage) {
                            return product.covered === product.total;
                        }

                        return product.covered > 0;
                    });
            }

            function buildDiffText(addedContainerIds) {
                return addedContainerIds
                    .map(function(containerId) {
                        return containerMap[containerId];
                    })
                    .filter(function(container) {
                        return container && container.snippet;
                    })
                    .sort(function(left, right) {
                        return left.title.localeCompare(right.title);
                    })
                    .map(function(container) {
                        return container.snippet;
                    })
                    .join("\n\n");
            }

            function updatePickerStates(targetIds, addedContainerIds) {
                root.querySelectorAll('[data-product-item]').forEach(function(item) {
                    var input = item.querySelector('[data-product-toggle]');
                    var product = productMap[input.value];
                    var coveredCount = (product.container_ids || []).filter(function(containerId) {
                        return targetIds.has(containerId);
                    }).length;
                    var meta = item.querySelector('[data-product-meta="' + product.id + '"]');

                    item.classList.toggle('is-selected', selectedProductIds.has(product.id));
                    item.classList.toggle('is-covered', coveredCount > 0);
                    item.classList.toggle('is-baseline', coveredCount > 0 && !selectedProductIds.has(product.id));

                    if (meta) {
                        meta.textContent = coveredCount + '/' + product.container_ids.length + ' Container im Zielbild';
                    }
                });

                root.querySelectorAll('[data-container-item]').forEach(function(item) {
                    var input = item.querySelector('[data-container-toggle]');
                    var container = containerMap[input.value];
                    var meta = item.querySelector('[data-container-meta="' + container.id + '"]');
                    var isBaseline = baselineContainerIds.has(container.id);
                    var isAdded = addedContainerIds.indexOf(container.id) !== -1;

                    item.classList.toggle('is-selected', selectedContainerIds.has(container.id));
                    item.classList.toggle('is-baseline', isBaseline);
                    item.classList.toggle('is-added', isAdded);

                    if (meta) {
                        if (isAdded) {
                            meta.textContent = 'Neu im Diff';
                        } else if (isBaseline) {
                            meta.textContent = 'Bereits in der Basis';
                        } else {
                            meta.textContent = (container.product_ids || []).length + ' Produkte';
                        }
                    }
                });
            }

            function updateSummaries() {
                var targetIds = getTargetContainerIds();
                var addedContainerIds = getAddedContainerIds(targetIds);
                var coveredProducts = getCoveredProducts(targetIds, false);
                var baselineProducts = getCoveredProducts(baselineContainerIds, true);
                var selectedProducts = products.filter(function(product) {
                    return selectedProductIds.has(product.id);
                }).map(function(product) {
                    var covered = (product.container_ids || []).filter(function(containerId) {
                        return targetIds.has(containerId);
                    }).length;

                    return {
                        label: product.label,
                        covered: covered,
                        total: product.container_ids.length,
                    };
                });
                var diffText = buildDiffText(addedContainerIds);
                var baselineServiceCount = parseComposeServices(composeInput.value).length;

                updatePickerStates(targetIds, addedContainerIds);

                root.querySelector('[data-baseline-service-count]').textContent = String(baselineServiceCount);
                root.querySelector('[data-baseline-container-count]').textContent = String(baselineContainerIds.size);
                root.querySelector('[data-baseline-product-count]').textContent = String(baselineProducts.length);
                root.querySelector('[data-added-service-count]').textContent = String(addedContainerIds.length);
                root.querySelector('[data-selected-container-count]').textContent = String(targetIds.size);
                root.querySelector('[data-selected-product-count]').textContent = String(selectedProducts.length);

                renderChipList(
                    root.querySelector('[data-baseline-products]'),
                    baselineProducts.map(function(product) {
                        return product.label;
                    }),
                    'info',
                    'Keine Produkte erkannt'
                );

                renderChipList(
                    root.querySelector('[data-baseline-containers]'),
                    Array.from(baselineContainerIds).map(function(containerId) {
                        return containerMap[containerId] ? containerMap[containerId].title : containerId;
                    }),
                    'muted',
                    'Keine Container erkannt'
                );

                renderChipList(
                    root.querySelector('[data-selected-products]'),
                    selectedProducts.map(function(product) {
                        return product.label + ' (' + product.covered + '/' + product.total + ')';
                    }),
                    'success',
                    'Noch nichts zusaetzlich ausgewaehlt'
                );

                renderChipList(
                    root.querySelector('[data-added-containers]'),
                    addedContainerIds.map(function(containerId) {
                        return containerMap[containerId] ? containerMap[containerId].title : containerId;
                    }),
                    'success',
                    'Keine neuen Container'
                );

                renderChipList(
                    root.querySelector('[data-unknown-services]'),
                    unknownServices,
                    'warning',
                    'Keine unbekannten Services'
                );

                root.querySelector('[data-unknown-services-group]').hidden = unknownServices.length === 0;

                diffOutput.value = diffText;
                diffCopyButton.setAttribute('data-copy-value', diffText);
                diffCopyButton.disabled = diffText.trim() === '';
            }

            function analyzeCompose() {
                var parsedServices = parseComposeServices(composeInput.value);

                baselineContainerIds = new Set();
                unknownServices = [];

                parsedServices.forEach(function(serviceTitle) {
                    var matchedContainer = containerTitleMap[normalizeKey(serviceTitle)];

                    if (matchedContainer) {
                        baselineContainerIds.add(matchedContainer.id);
                    } else {
                        unknownServices.push(serviceTitle);
                    }
                });

                updateSummaries();
            }

            function filterPicker(searchInput, selector) {
                var query = normalizeKey(searchInput.value);

                root.querySelectorAll(selector).forEach(function(item) {
                    var haystack = normalizeKey(item.getAttribute('data-search'));
                    item.hidden = query !== '' && haystack.indexOf(query) === -1;
                });
            }

            function copyText(value) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(value);
                }

                return new Promise(function(resolve, reject) {
                    var textarea = document.createElement('textarea');
                    textarea.value = value;
                    textarea.setAttribute('readonly', 'readonly');
                    textarea.style.position = 'absolute';
                    textarea.style.left = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.select();

                    try {
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        resolve();
                    } catch (error) {
                        document.body.removeChild(textarea);
                        reject(error);
                    }
                });
            }

            function flashCopyState(button) {
                button.classList.add('is-copied', 'show-copy-tooltip');
                window.setTimeout(function() {
                    button.classList.remove('is-copied', 'show-copy-tooltip');
                }, 1200);
            }

            root.querySelectorAll('[data-product-toggle]').forEach(function(input) {
                input.addEventListener('change', function() {
                    if (input.checked) {
                        selectedProductIds.add(input.value);
                    } else {
                        selectedProductIds.delete(input.value);
                    }

                    updateSummaries();
                });
            });

            root.querySelectorAll('[data-container-toggle]').forEach(function(input) {
                input.addEventListener('change', function() {
                    if (input.checked) {
                        selectedContainerIds.add(input.value);
                    } else {
                        selectedContainerIds.delete(input.value);
                    }

                    updateSummaries();
                });
            });

            productSearch.addEventListener('input', function() {
                filterPicker(productSearch, '[data-product-item]');
            });

            containerSearch.addEventListener('input', function() {
                filterPicker(containerSearch, '[data-container-item]');
            });

            root.querySelector('[data-compose-analyze]').addEventListener('click', function() {
                analyzeCompose();
            });

            root.querySelector('[data-compose-reset]').addEventListener('click', function() {
                composeInput.value = data.saved_compose_raw || '';
                analyzeCompose();
            });

            root.querySelector('[data-compose-clear]').addEventListener('click', function() {
                composeInput.value = '';
                analyzeCompose();
            });

            composeInput.addEventListener('input', function() {
                window.clearTimeout(analyzeTimer);
                analyzeTimer = window.setTimeout(function() {
                    analyzeCompose();
                }, 250);
            });

            diffCopyButton.addEventListener('click', function() {
                if ((diffOutput.value || '').trim() === '') {
                    return;
                }

                copyText(diffOutput.value).then(function() {
                    flashCopyState(diffCopyButton);
                });
            });

            analyzeCompose();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initServerComposeWorkspace);
            } else {
                initServerComposeWorkspace();
            }

            window.addEventListener('load', initServerComposeWorkspace);
        })();
    </script>
@endsection
