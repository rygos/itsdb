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

                var composeInput = root.querySelector('[data-compose-input]');
                var diffOutput = root.querySelector('[data-compose-diff-output]');
                var diffCopyButton = root.querySelector('[data-compose-diff-copy]');
                var debugOutput = root.querySelector('[data-compose-debug]');
                var productSearch = root.querySelector('[data-product-search]');
                var containerSearch = root.querySelector('[data-container-search]');
                var analyzeTimer = null;

                function normalizeId(value) {
                    return String(value || '');
                }

                function normalizeKey(value) {
                    return String(value || '')
                        .trim()
                        .replace(/^['"]|['"]$/g, '')
                        .toLowerCase();
                }

                function uniqueValues(values) {
                    return Array.from(new Set((values || []).map(function(value) {
                        return normalizeId(value);
                    }).filter(function(value) {
                        return value !== '';
                    })));
                }

                function getProductDisplayLabel(product) {
                    var label = String(product.label || '').trim();
                    if (label !== '') {
                        return label;
                    }

                    var subtitleParts = [product.category || '', product.function || ''].filter(function(part) {
                        return String(part).trim() !== '';
                    });

                    if (subtitleParts.length > 0) {
                        return subtitleParts.join(' / ');
                    }

                    return normalizeId(product.id);
                }

                function parseComposeServices(text) {
                    var services = [];
                    var lines = String(text || '').split(/\r\n|\n|\r/);
                    var inServices = false;
                    var servicesIndent = 0;

                    lines.forEach(function(line) {
                        var trimmed = String(line || '').trim();
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

                    return uniqueValues(services);
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

                function sortByTitle(ids, itemMap) {
                    return ids.sort(function(left, right) {
                        var leftTitle = itemMap[left] ? itemMap[left].title || itemMap[left].display_label || left : left;
                        var rightTitle = itemMap[right] ? itemMap[right].title || itemMap[right].display_label || right : right;

                        return leftTitle.localeCompare(rightTitle);
                    });
                }

                var containers = (data.containers || []).map(function(container) {
                    return {
                        id: normalizeId(container.id),
                        title: String(container.title || ''),
                        search: String(container.search || ''),
                        snippet: String(container.snippet || ''),
                        product_ids: uniqueValues(container.product_ids),
                        product_labels: (container.product_labels || []).map(function(label) {
                            return String(label || '');
                        }),
                    };
                });

                var products = (data.products || []).map(function(product) {
                    return {
                        id: normalizeId(product.id),
                        label: String(product.label || ''),
                        category: String(product.category || ''),
                        function: String(product.function || ''),
                        search: String(product.search || ''),
                        container_ids: uniqueValues(product.container_ids),
                    };
                }).map(function(product) {
                    product.display_label = getProductDisplayLabel(product);

                    return product;
                });

                var containerMap = {};
                var containerTitleMap = {};
                containers.forEach(function(container) {
                    containerMap[container.id] = container;
                    containerTitleMap[normalizeKey(container.title)] = container;
                });

                var productMap = {};
                products.forEach(function(product) {
                    productMap[product.id] = product;
                });

                var selectedContainerIds = new Set();
                var baselineContainerIds = new Set();
                var unknownServices = [];

                function getSelectedProductIds() {
                    return new Set(
                        Array.from(root.querySelectorAll('[data-product-checkbox]:checked')).map(function(input) {
                            return normalizeId(input.value || input.getAttribute('data-product-checkbox'));
                        })
                    );
                }

                function getSelectedProducts() {
                    return sortByTitle(Array.from(getSelectedProductIds()), productMap).map(function(productId) {
                        return productMap[productId];
                    }).filter(function(product) {
                        return !!product;
                    });
                }

                function getSelectedProductContainerIds(selectedProducts) {
                    var selectedIds = new Set();

                    selectedProducts.forEach(function(product) {
                        (product.container_ids || []).forEach(function(containerId) {
                            if (containerMap[containerId]) {
                                selectedIds.add(containerId);
                            }
                        });
                    });

                    return selectedIds;
                }

                function getTargetContainerIds(selectedProducts) {
                    var targetIds = new Set(Array.from(baselineContainerIds));

                    selectedContainerIds.forEach(function(containerId) {
                        if (containerMap[containerId]) {
                            targetIds.add(containerId);
                        }
                    });

                    getSelectedProductContainerIds(selectedProducts).forEach(function(containerId) {
                        targetIds.add(containerId);
                    });

                    return targetIds;
                }

                function getCoveredProducts(targetIds, requireFullCoverage) {
                    return products.map(function(product) {
                        var covered = (product.container_ids || []).filter(function(containerId) {
                            return targetIds.has(containerId);
                        }).length;

                        return {
                            label: product.display_label,
                            covered: covered,
                            total: product.container_ids.length,
                        };
                    }).filter(function(product) {
                        if (product.total === 0) {
                            return false;
                        }

                        return requireFullCoverage ? product.covered === product.total : product.covered > 0;
                    });
                }

                function buildDiffText(addedContainerIds) {
                    return addedContainerIds
                        .map(function(containerId) {
                            return containerMap[containerId];
                        })
                        .filter(function(container) {
                            return container && container.snippet.trim() !== '';
                        })
                        .map(function(container) {
                            return container.snippet;
                        })
                        .join("\n\n");
                }

                function filterPicker(input, selector) {
                    var query = normalizeKey(input.value);

                    root.querySelectorAll(selector).forEach(function(item) {
                        var haystack = normalizeKey(item.getAttribute('data-search'));
                        item.style.display = query !== '' && haystack.indexOf(query) === -1 ? 'none' : '';
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

                function updatePickerStates(targetIds, selectedProductIds, addedContainerIds) {
                    root.querySelectorAll('[data-product-item]').forEach(function(item) {
                        var productId = normalizeId(item.getAttribute('data-product-toggle'));
                        var product = productMap[productId];
                        var coveredCount = product ? (product.container_ids || []).filter(function(containerId) {
                            return targetIds.has(containerId);
                        }).length : 0;
                        var meta = item.querySelector('[data-product-meta="' + productId + '"]');

                        item.classList.toggle('is-selected', selectedProductIds.has(productId));
                        item.classList.toggle('is-covered', coveredCount > 0);
                        item.classList.toggle('is-baseline', coveredCount > 0 && !selectedProductIds.has(productId));

                        if (meta && product) {
                            meta.textContent = product.container_ids.length === 0
                                ? 'Kein Container-Mapping'
                                : coveredCount + '/' + product.container_ids.length + ' Container im Zielbild';
                        }
                    });

                    root.querySelectorAll('[data-container-item]').forEach(function(item) {
                        var containerId = normalizeId(item.getAttribute('data-container-toggle'));
                        var meta = item.querySelector('[data-container-meta="' + containerId + '"]');
                        var isBaseline = baselineContainerIds.has(containerId);
                        var isAdded = addedContainerIds.indexOf(containerId) !== -1;

                        item.classList.toggle('is-selected', selectedContainerIds.has(containerId));
                        item.classList.toggle('is-baseline', isBaseline);
                        item.classList.toggle('is-added', isAdded);

                        if (meta) {
                            if (isAdded) {
                                meta.textContent = 'Neu im Diff';
                            } else if (isBaseline) {
                                meta.textContent = 'Bereits in der Basis';
                            } else {
                                meta.textContent = (containerMap[containerId] && containerMap[containerId].product_ids.length > 0)
                                    ? containerMap[containerId].product_ids.length + ' Produkte'
                                    : 'Kein Produkt-Mapping';
                            }
                        }
                    });
                }

                function updateSummaries() {
                    var selectedProducts = getSelectedProducts();
                    var selectedProductIds = new Set(selectedProducts.map(function(product) {
                        return product.id;
                    }));
                    var selectedProductContainerIds = getSelectedProductContainerIds(selectedProducts);
                    var targetIds = getTargetContainerIds(selectedProducts);
                    var addedContainerIds = sortByTitle(Array.from(targetIds).filter(function(containerId) {
                        return !baselineContainerIds.has(containerId);
                    }), containerMap);
                    var selectedProductSummaries = selectedProducts.map(function(product) {
                        var covered = (product.container_ids || []).filter(function(containerId) {
                            return targetIds.has(containerId);
                        }).length;

                        return product.display_label + ' (' + covered + '/' + product.container_ids.length + ')';
                    });
                    var baselineProducts = getCoveredProducts(baselineContainerIds, true).map(function(product) {
                        return product.label;
                    });
                    var diffText = buildDiffText(addedContainerIds);

                    updatePickerStates(targetIds, selectedProductIds, addedContainerIds);

                    root.querySelector('[data-baseline-service-count]').textContent = String(parseComposeServices(composeInput.value).length);
                    root.querySelector('[data-baseline-container-count]').textContent = String(baselineContainerIds.size);
                    root.querySelector('[data-baseline-product-count]').textContent = String(baselineProducts.length);
                    root.querySelector('[data-added-service-count]').textContent = String(addedContainerIds.length);
                    root.querySelector('[data-selected-container-count]').textContent = String(targetIds.size);
                    root.querySelector('[data-selected-product-count]').textContent = String(selectedProducts.length);

                    if (debugOutput) {
                        debugOutput.textContent = [
                            'Auswahl: ' + selectedProducts.length + ' Produkte',
                            selectedContainerIds.size + ' Container direkt',
                            selectedProductContainerIds.size + ' Container aus Produkten',
                            addedContainerIds.length + ' neue Container',
                            diffText.length + ' YAML-Zeichen',
                        ].join(', ');
                    }

                    renderChipList(root.querySelector('[data-baseline-products]'), baselineProducts, 'info', 'Keine Produkte erkannt');
                    renderChipList(
                        root.querySelector('[data-baseline-containers]'),
                        sortByTitle(Array.from(baselineContainerIds), containerMap).map(function(containerId) {
                            return containerMap[containerId] ? containerMap[containerId].title : containerId;
                        }),
                        'muted',
                        'Keine Container erkannt'
                    );
                    renderChipList(root.querySelector('[data-selected-products]'), selectedProductSummaries, 'success', 'Noch nichts zusaetzlich ausgewaehlt');
                    renderChipList(
                        root.querySelector('[data-added-containers]'),
                        addedContainerIds.map(function(containerId) {
                            return containerMap[containerId] ? containerMap[containerId].title : containerId;
                        }),
                        'success',
                        'Keine neuen Container'
                    );
                    renderChipList(root.querySelector('[data-unknown-services]'), unknownServices, 'warning', 'Keine unbekannten Services');
                    root.querySelector('[data-unknown-services-group]').hidden = unknownServices.length === 0;

                    diffOutput.value = diffText;
                    diffCopyButton.setAttribute('data-copy-value', diffText);
                    diffCopyButton.disabled = diffText.trim() === '';
                }

                function analyzeCompose() {
                    baselineContainerIds = new Set();
                    unknownServices = [];

                    parseComposeServices(composeInput.value).forEach(function(serviceTitle) {
                        var matchedContainer = containerTitleMap[normalizeKey(serviceTitle)];
                        if (matchedContainer) {
                            baselineContainerIds.add(matchedContainer.id);
                        } else {
                            unknownServices.push(serviceTitle);
                        }
                    });

                    updateSummaries();
                }

                root.addEventListener('input', function(event) {
                    if (event.target.matches('[data-product-search]')) {
                        filterPicker(productSearch, '[data-product-item]');
                    }

                    if (event.target.matches('[data-container-search]')) {
                        filterPicker(containerSearch, '[data-container-item]');
                    }

                    if (event.target.matches('[data-compose-input]')) {
                        window.clearTimeout(analyzeTimer);
                        analyzeTimer = window.setTimeout(analyzeCompose, 250);
                    }
                });

                root.addEventListener('change', function(event) {
                    if (event.target.matches('[data-product-checkbox]')) {
                        updateSummaries();
                        return;
                    }

                    if (event.target.matches('[data-container-checkbox]')) {
                        var containerId = normalizeId(event.target.value || event.target.getAttribute('data-container-checkbox'));
                        if (event.target.checked) {
                            selectedContainerIds.add(containerId);
                        } else {
                            selectedContainerIds.delete(containerId);
                        }
                        updateSummaries();
                    }
                });

                root.addEventListener('click', function(event) {
                    var containerItem = event.target.closest('[data-container-item]');
                    if (containerItem) {
                        event.preventDefault();
                        var containerId = normalizeId(containerItem.getAttribute('data-container-toggle'));
                        if (selectedContainerIds.has(containerId)) {
                            selectedContainerIds.delete(containerId);
                        } else {
                            selectedContainerIds.add(containerId);
                        }
                        updateSummaries();
                        return;
                    }

                    var trigger = event.target.closest('[data-compose-analyze], [data-compose-reset], [data-compose-clear], [data-compose-diff-copy]');
                    if (!trigger) {
                        return;
                    }

                    if (trigger.matches('[data-compose-analyze]')) {
                        analyzeCompose();
                        return;
                    }

                    if (trigger.matches('[data-compose-reset]')) {
                        composeInput.value = data.saved_compose_raw || '';
                        analyzeCompose();
                        return;
                    }

                    if (trigger.matches('[data-compose-clear]')) {
                        composeInput.value = '';
                        analyzeCompose();
                        return;
                    }

                    if (trigger.matches('[data-compose-diff-copy]') && diffOutput.value.trim() !== '') {
                        copyText(diffOutput.value).then(function() {
                            flashCopyState(diffCopyButton);
                        });
                    }
                });

                analyzeCompose();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initServerComposeWorkspace);
            } else {
                initServerComposeWorkspace();
            }

            window.addEventListener('load', initServerComposeWorkspace);
            window.setTimeout(initServerComposeWorkspace, 50);
        })();
    </script>
@endsection
