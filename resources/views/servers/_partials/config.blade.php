<tr>
    <th>Compose Workspace</th>
</tr>
<tr>
    <td>
        <div class="server-compose-workspace" data-compose-workspace>
            <script type="application/json" data-compose-workspace-json>@json($composeWorkspaceData)</script>
            <aside class="server-compose-workspace__sidebar">
                <section class="server-compose-card">
                    <div class="server-compose-card__header">
                        <div>
                            <h3>Compose Quellen</h3>
                            <p>Bestehende Vorlagen und schnelle Verknuepfung fuer diesen Server.</p>
                        </div>
                    </div>

                    @if($compose_select !== [])
                        {{ html()->form()->route('servers.add_composer', $server->id)->class('server-compose-source-form')->open() }}
                        <label for="server-compose-source-select">Compose hinzufuegen</label>
                        {{ html()->select('compose', $compose_select)->id('server-compose-source-select') }}
                        {{ html()->submit('Verknuepfen') }}
                        {{ html()->form()->close() }}
                    @else
                        <div class="server-compose-empty-state">Keine weiteren Compose-Vorlagen verfuegbar.</div>
                    @endif

                    <div class="server-compose-source-list">
                        @forelse($compose as $item)
                            <article class="server-compose-source-list__item">
                                <div>
                                    <strong>
                                        <a href="{{ route('compose.show', $item->composer->compose_filename) }}">
                                            {{ $item->composer->title }}
                                        </a>
                                    </strong>
                                    @if(! is_null($item->composer->title_alternatives))
                                        <div>{{ $item->composer->title_alternatives }}</div>
                                    @endif
                                    <small>
                                        {{ $item->composer->rel->count() }} Container:
                                        {{ $item->composer->rel->pluck('container.title')->filter()->implode(', ') ?: '-' }}
                                    </small>
                                </div>
                                <a href="{{ route('servers.del_composer', [$server->id, $item->composer_id]) }}" class="itsdb-action-control">delete</a>
                            </article>
                        @empty
                            <div class="server-compose-empty-state">Noch keine Compose-Vorlagen mit diesem Server verknuepft.</div>
                        @endforelse
                    </div>
                </section>

                <section class="server-compose-card">
                    <div class="server-compose-card__header">
                        <div>
                            <h3>Produkte hinzufuegen</h3>
                            <p>Produktauswahl zieht automatisch alle zugeordneten Container in den Diff.</p>
                        </div>
                    </div>

                    <div class="server-compose-hint">
                        1. Optional Compose analysieren oder gespeicherte Compose laden.
                        2. Dann Produkte oder Container auswaehlen.
                        3. Rechts nur die neu hinzukommenden Services kopieren.
                    </div>

                    <input type="text" class="server-compose-search" placeholder="Produkt suchen" data-product-search>

                    <div class="server-compose-picker" data-product-list>
                        @foreach($composeWorkspaceData['products'] as $product)
                            <button type="button" class="server-compose-picker__item" data-product-item data-product-toggle="{{ $product['id'] }}" data-search="{{ $product['search'] }}">
                                <span class="server-compose-picker__body">
                                    <strong>{{ $product['label'] }}</strong>
                                    <small>{{ $product['category'] }} / {{ $product['function'] }}</small>
                                    <small data-product-meta="{{ $product['id'] }}">{{ count($product['container_ids']) }} Container</small>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </section>

                <section class="server-compose-card">
                    <div class="server-compose-card__header">
                        <div>
                            <h3>Container hinzufuegen</h3>
                            <p>Direkte Containerauswahl fuer Sonderfaelle oder Teilmengen.</p>
                        </div>
                    </div>

                    <input type="text" class="server-compose-search" placeholder="Container suchen" data-container-search>

                    <div class="server-compose-picker" data-container-list>
                        @foreach($composeWorkspaceData['containers'] as $container)
                            <button type="button" class="server-compose-picker__item" data-container-item data-container-toggle="{{ $container['id'] }}" data-search="{{ $container['search'] }}">
                                <span class="server-compose-picker__body">
                                    <strong>{{ $container['title'] }}</strong>
                                    <small>{{ implode(', ', $container['product_labels']) ?: 'Kein Produkt-Mapping' }}</small>
                                    <small data-container-meta="{{ $container['id'] }}">{{ count($container['product_ids']) }} Produkte</small>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </section>
            </aside>

            <div class="server-compose-workspace__main">
                {{ html()->form()->route('servers.update_serverconfig')->class('server-compose-editor-form')->open() }}
                {{ html()->hidden('server_id', $server->id) }}

                <section class="server-compose-card server-compose-card--editor">
                    <div class="server-compose-card__header">
                        <div>
                            <h3>docker-compose Analyse</h3>
                            <p>Bestehende Compose einfuegen oder die gespeicherte Version analysieren. Neue Produkte und Container werden gegen diese Basis diffed.</p>
                        </div>
                        <div class="server-compose-toolbar">
                            <button type="button" data-compose-analyze>Analyse</button>
                            <button type="button" data-compose-reset>Gespeicherte Compose laden</button>
                            <button type="button" data-compose-clear>Leeren</button>
                            <a href="{{ route('compose.generate', $server->id) }}">Compose generieren</a>
                        </div>
                    </div>

                    <div class="server-compose-hint">
                        Hinweise: Die Analyse erkennt Services aus dem `services:`-Block. Produkte in der Basis gelten nur dann als erkannt, wenn alle zugeordneten Container vorhanden sind.
                    </div>

                    {{ html()->textarea('docker_compose', $server->docker_compose_raw)->class('server-compose-editor__textarea')->attribute('data-compose-input', 'true') }}
                </section>

                <div class="server-compose-workspace__summary-grid">
                    <section class="server-compose-card">
                        <div class="server-compose-card__header">
                            <div>
                                <h3>Erkannte Basis</h3>
                                <p>Installierte Services, Container und Produkte laut analysierter Compose.</p>
                            </div>
                        </div>

                        <div class="server-compose-stats">
                            <article class="server-compose-stat">
                                <strong data-baseline-service-count>0</strong>
                                <span>Services erkannt</span>
                            </article>
                            <article class="server-compose-stat">
                                <strong data-baseline-container-count>0</strong>
                                <span>Container erkannt</span>
                            </article>
                            <article class="server-compose-stat">
                                <strong data-baseline-product-count>0</strong>
                                <span>Produkte erkannt</span>
                            </article>
                        </div>

                        <div class="server-compose-summary-group">
                            <h4>Produkte</h4>
                            <div class="server-compose-chip-list" data-baseline-products></div>
                        </div>

                        <div class="server-compose-summary-group">
                            <h4>Container</h4>
                            <div class="server-compose-chip-list" data-baseline-containers></div>
                        </div>

                        <div class="server-compose-summary-group" data-unknown-services-group hidden>
                            <h4>Unbekannte Services</h4>
                            <div class="server-compose-chip-list server-compose-chip-list--warning" data-unknown-services></div>
                        </div>
                    </section>

                    <section class="server-compose-card">
                        <div class="server-compose-card__header">
                            <div>
                                <h3>Neu hinzukommende Services</h3>
                                <p>Nur Services, die noch nicht in der Basis vorhanden sind. Direkt kopierbar wie in der Produktmatrix.</p>
                            </div>
                        </div>

                        <div class="server-compose-stats">
                            <article class="server-compose-stat">
                                <strong data-added-service-count>0</strong>
                                <span>Neue Services</span>
                            </article>
                            <article class="server-compose-stat">
                                <strong data-selected-container-count>0</strong>
                                <span>Container in Auswahl</span>
                            </article>
                            <article class="server-compose-stat">
                                <strong data-selected-product-count>0</strong>
                                <span>Produkte in Auswahl</span>
                            </article>
                        </div>

                        <div class="server-compose-debug" data-compose-debug>
                            Auswahl: 0 Produkte, 0 Container, 0 neue Container, 0 YAML-Zeichen
                        </div>

                        <div class="server-compose-summary-group">
                            <h4>Ausgewaehlte Produkte</h4>
                            <div class="server-compose-chip-list" data-selected-products></div>
                        </div>

                        <div class="server-compose-summary-group">
                            <h4>Neu hinzukommende Container</h4>
                            <div class="server-compose-chip-list" data-added-containers></div>
                        </div>

                        {{ html()->textarea('generated_services_preview')->attribute('readonly', true)->attribute('data-compose-diff-output', 'true')->class('server-compose-diff__textarea') }}

                        <div class="server-compose-diff__actions">
                            <button
                                type="button"
                                class="itsdb-copy-button"
                                data-compose-diff-copy
                                data-copy-tooltip="Kopiert"
                                data-copy-value=""
                                title="Neue Services kopieren"
                            >
                                <span>Neue Services kopieren</span>
                            </button>
                        </div>
                    </section>
                </div>

                <section class="server-compose-card server-compose-card--env">
                    <div class="server-compose-card__header">
                        <div>
                            <h3>.env Rohtext</h3>
                            <p>Getrennt vom Compose-Editor, damit fuer den Compose-Diff mehr Platz bleibt.</p>
                        </div>
                        <div class="server-compose-toolbar">
                            <a href="{{ route('env.generate_from_raw', $server->id) }}">Infos aus Rohtext erzeugen</a>
                            <a href="{{ route('env.generate', $server->id) }}">Variablen aus Compose erzeugen</a>
                        </div>
                    </div>

                    {{ html()->textarea('env', $server->env_raw)->class('server-compose-editor__textarea server-compose-editor__textarea--env') }}
                </section>

                <div class="server-compose-savebar">
                    {{ html()->submit('Compose und .env speichern') }}
                </div>

                {{ html()->form()->close() }}

                <section class="server-compose-card">
                    <div class="server-compose-card__header">
                        <div>
                            <h3>.env Variablen</h3>
                            <p>Fehlende oder optionale Variablen koennen weiterhin direkt gepflegt werden.</p>
                        </div>
                    </div>

                    {{ html()->form()->route('env.update', $server->id)->open() }}
                    <table class="server-compose-env-table">
                        <tr>
                            <th>Key</th>
                            <th>Variable</th>
                        </tr>
                        @foreach($env as $item)
                            @php($color = $item->needed == 1 ? 'red' : 'orange')
                            <tr>
                                <td style="color: {{ $color }}">{{ $item->key }}</td>
                                <td>{{ html()->text($item->key, $item->value) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2">{{ html()->submit('Variablen speichern') }}</td>
                        </tr>
                    </table>
                    {{ html()->form()->close() }}
                </section>
            </div>
        </div>
    </td>
</tr>
