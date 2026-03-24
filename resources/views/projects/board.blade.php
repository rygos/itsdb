@extends('layouts.app')
@section('title', 'Projekt-Board')
@section('content')
    <style>
        #prodpagecontainer.project-board-page,
        #prodpagecontainer.project-board-page #pouetbox_prodmain {
            width: 100%;
        }
        .project-board {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            align-items: start;
            width: 100%;
        }
        .project-board,
        .project-board * {
            box-sizing: border-box;
        }
        .project-board__column {
            background: #00001a;
            color: #ffffe0;
            border: 1px solid #17395c;
            padding: 12px;
            min-width: 0;
        }
        .project-board__column--new {
            box-shadow: inset 0 4px 0 0 #4d6d8c;
        }
        .project-board__column--in-progress {
            box-shadow: inset 0 4px 0 0 #2f6f9f;
        }
        .project-board__column--blocked {
            box-shadow: inset 0 4px 0 0 #8f3b52;
        }
        .project-board__column--finished {
            box-shadow: inset 0 4px 0 0 #2f7a57;
        }
        .project-board__header {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #17395c;
        }
        .project-board__meta {
            font-size: 12px;
            color: inherit;
        }
        .project-board__stack {
            display: grid;
            gap: 12px;
            min-width: 0;
        }
        .project-board__stack.is-drop-target {
            outline: 2px dashed #ffbf00;
            outline-offset: 4px;
        }
        .project-card {
            background: #0a1630;
            color: #ffffe0;
            border: 1px solid #17395c;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 224, 0.06);
            padding: 10px;
            width: 100%;
            min-width: 0;
            cursor: grab;
        }
        .project-card.is-dragging {
            opacity: 0.55;
            cursor: grabbing;
        }
        .project-card__title {
            font-weight: bold;
            margin-bottom: 6px;
            overflow-wrap: anywhere;
        }
        .project-card__title a,
        .project-card__line a {
            color: #ffbf00;
        }
        .project-card__title a:hover,
        .project-card__line a:hover {
            color: #ffdf00;
        }
        .project-card__line {
            font-size: 12px;
            margin-bottom: 4px;
        }
        .project-card__status {
            display: inline-block;
            padding: 2px 6px;
            margin-bottom: 8px;
            border: 1px solid #295785;
        }
        .project-card__form {
            margin-top: 8px;
        }
        .project-card__hint {
            margin-top: 10px;
            font-size: 11px;
            color: #9db5cf;
        }
        @media (max-width: 1100px) {
            .project-board {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }
        }
        @media (max-width: 700px) {
            .project-board {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div id="prodpagecontainer" class="project-board-page">
        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th>
                    <span id="title"><big>Projekt-Pipeline</big></span>
                    <div id="nfo">Kanban-Ansicht fuer deine Projekte nach Statusgruppe. Projekte koennen per Maus zwischen den Spalten verschoben werden.</div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div class="project-board">
                        @foreach($boardColumns as $columnKey => $column)
                            @php
                                $columnClass = match ($columnKey) {
                                    'new' => 'project-board__column--new',
                                    'in_progress' => 'project-board__column--in-progress',
                                    'blocked' => 'project-board__column--blocked',
                                    'finished' => 'project-board__column--finished',
                                    default => '',
                                };
                            @endphp
                            <section class="project-board__column {{ $columnClass }}">
                                <div class="project-board__header">
                                    <div><strong>{{ $column['label'] }}</strong></div>
                                    <div class="project-board__meta">{{ $column['count'] }} Projekte | {{ $column['hours'] }} h</div>
                                    @if($loop->last)
                                        <div class="project-board__meta">Nur Abschluesse der letzten 30 Tage</div>
                                    @endif
                                </div>
                                <div
                                    class="project-board__stack"
                                    data-project-column="{{ $columnKey }}"
                                    data-finished-status-id="{{ $loop->last ? $finishedStatusId : '' }}"
                                >
                                    @forelse($column['projects'] as $project)
                                        @php
                                            $statusName = optional($project->status)->name;
                                            $statusAccent = match ($statusName) {
                                                'NEW', 'OPEN' => ['background' => 'rgba(77, 109, 140, 0.22)', 'border' => '#4d6d8c', 'color' => '#d9ecff'],
                                                'WIP', 'CHECK' => ['background' => 'rgba(47, 111, 159, 0.22)', 'border' => '#2f6f9f', 'color' => '#d7eeff'],
                                                'WAIT FOR INFO', 'ON HOLD', 'BLOCKED', 'BLOCKIERT' => ['background' => 'rgba(143, 59, 82, 0.25)', 'border' => '#8f3b52', 'color' => '#ffd8e2'],
                                                'FINISHED', 'DONE', 'CLOSED', 'ERLEDIGT' => ['background' => 'rgba(47, 122, 87, 0.24)', 'border' => '#2f7a57', 'color' => '#dcffe8'],
                                                default => ['background' => 'rgba(23, 57, 92, 0.55)', 'border' => '#295785', 'color' => '#ffffe0'],
                                            };
                                        @endphp
                                        <article
                                            class="project-card"
                                            draggable="true"
                                            data-project-card="true"
                                            data-project-id="{{ $project->id }}"
                                            data-current-end-date="{{ optional($project->end_date)->format('Y-m-d') }}"
                                            data-current-end-date-display="{{ optional($project->end_date)->format('d.m.Y') }}"
                                        >
                                            <div class="project-card__title">
                                                <a href="{{ route('projects.view', $project) }}">{{ $project->name }}</a>
                                            </div>
                                            <div
                                                class="project-card__status"
                                                style="background-color: {{ $statusAccent['background'] }}; border-color: {{ $statusAccent['border'] }}; color: {{ $statusAccent['color'] }};"
                                            >
                                                {{ $statusName ?? 'Ohne Status' }}
                                            </div>
                                            <div class="project-card__line"><strong>Dynamics:</strong> {{ $project->dynamics_id }}</div>
                                            <div class="project-card__line">
                                                <strong>Kunde:</strong>
                                                @if($project->customer)
                                                    <a href="{{ route('customers.view', $project->customer) }}">{{ $project->customer->name }}</a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="project-card__line"><strong>Ort:</strong> {{ $project->customer?->city?->name ?? 'Kein Ort' }}</div>
                                            <div class="project-card__line"><strong>Start:</strong> {{ optional($project->start_date)->format('d.m.Y') ?? '-' }}</div>
                                            <div class="project-card__line"><strong>Ende:</strong> {{ optional($project->end_date)->format('d.m.Y') ?? '-' }}</div>
                                            <div class="project-card__line"><strong>Stunden:</strong> {{ $project->hours ?? '-' }}</div>
                                            <div class="project-card__hint">Per Drag-and-Drop verschieben</div>
                                            <form method="POST" action="{{ route('projects.change_status') }}" class="project-card__form" hidden>
                                                @csrf
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <input type="hidden" name="finished_end_date_action" value="keep" data-finished-end-date-action="true">
                                                <input type="hidden" name="status" value="{{ $project->status_id }}" data-project-status-input="true">
                                            </form>
                                        </article>
                                    @empty
                                        <div class="project-card">Keine Projekte.</div>
                                    @endforelse
                                </div>
                            </section>
                        @endforeach
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="itsdb-modal" id="project-board-blocked-status-modal" aria-hidden="true">
        <div class="itsdb-modal__dialog">
            <div class="itsdb-modal__header">
                <div class="itsdb-modal__title">Blockierten Status waehlen</div>
                <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
            </div>
            <div class="itsdb-modal__body">
                <p id="project-board-blocked-status-message" style="margin-bottom: 12px;">
                    Bitte waehle den passenden blockierten Status.
                </p>
                <div style="margin-bottom: 12px;">
                    <select id="project-board-blocked-status-select" style="width: 100%;">
                        @foreach($blockedStatusOptions as $statusId => $statusName)
                            <option value="{{ $statusId }}">{{ $statusName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="itsdb-actions">
                    <button type="button" id="project-board-blocked-status-confirm">Status setzen</button>
                    <button type="button" data-modal-close>Abbrechen</button>
                </div>
            </div>
        </div>
    </div>
    <div class="itsdb-modal" id="project-board-finished-end-date-modal" aria-hidden="true">
        <div class="itsdb-modal__dialog">
            <div class="itsdb-modal__header">
                <div class="itsdb-modal__title">Enddatum beim Abschluss anpassen</div>
                <button type="button" class="itsdb-modal__close" data-modal-close>Schliessen</button>
            </div>
            <div class="itsdb-modal__body">
                <p id="project-board-finished-end-date-message" style="margin-bottom: 12px;">
                    Dieses Projekt hat aktuell ein anderes Enddatum als heute.
                </p>
                <div class="itsdb-actions">
                    <button type="button" id="project-board-finished-end-date-confirm">Ja, auf aktuellen Tag setzen</button>
                    <button type="button" id="project-board-finished-end-date-keep">Alten Tag lassen</button>
                    <button type="button" data-modal-close>Abbrechen</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var board = document.querySelector('.project-board');
            if (!board) {
                return;
            }

            var draggedCard = null;
            var pendingCard = null;
            var columnStatusMap = @json($boardDropStatuses);
            var blockedModal = document.getElementById('project-board-blocked-status-modal');
            var blockedMessage = document.getElementById('project-board-blocked-status-message');
            var blockedSelect = document.getElementById('project-board-blocked-status-select');
            var blockedConfirmButton = document.getElementById('project-board-blocked-status-confirm');
            var finishedModal = document.getElementById('project-board-finished-end-date-modal');
            var finishedMessage = document.getElementById('project-board-finished-end-date-message');
            var finishedConfirmButton = document.getElementById('project-board-finished-end-date-confirm');
            var finishedKeepButton = document.getElementById('project-board-finished-end-date-keep');

            function setModalState(modal, isOpen) {
                if (!modal) {
                    return;
                }

                modal.style.display = isOpen ? 'flex' : 'none';
                modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            }

            function resetPendingCard() {
                pendingCard = null;
            }

            function cardForm(card) {
                return card ? card.querySelector('.project-card__form') : null;
            }

            function cardStatusInput(card) {
                return card ? card.querySelector('[data-project-status-input="true"]') : null;
            }

            function cardFinishedActionInput(card) {
                return card ? card.querySelector('[data-finished-end-date-action="true"]') : null;
            }

            function submitCard(card, statusId, finishedEndDateAction) {
                var form = cardForm(card);
                var statusInput = cardStatusInput(card);
                var finishedActionInput = cardFinishedActionInput(card);

                if (!form || !statusInput) {
                    return;
                }

                statusInput.value = statusId;

                if (finishedActionInput) {
                    finishedActionInput.value = finishedEndDateAction || 'keep';
                }

                form.submit();
            }

            function openBlockedModal(card) {
                if (!blockedModal || !blockedMessage || !blockedSelect) {
                    return;
                }

                pendingCard = card;

                var projectName = card.querySelector('.project-card__title a');
                blockedMessage.textContent = '"' + (projectName ? projectName.textContent.trim() : 'Das Projekt') + '" wurde auf Blockiert gezogen. Bitte waehle den passenden Status.';
                setModalState(blockedModal, true);
            }

            function openFinishedModal(card) {
                if (!finishedModal || !finishedMessage) {
                    return;
                }

                pendingCard = card;

                var currentEndDate = card.getAttribute('data-current-end-date');
                var currentEndDateDisplay = card.getAttribute('data-current-end-date-display') || currentEndDate || '-';
                var projectName = card.querySelector('.project-card__title a');
                var today = new Date();
                var todayDisplay = [
                    String(today.getDate()).padStart(2, '0'),
                    String(today.getMonth() + 1).padStart(2, '0'),
                    today.getFullYear()
                ].join('.');

                finishedMessage.textContent = '"' + (projectName ? projectName.textContent.trim() : 'Das Projekt') + '" hat aktuell das Enddatum ' + currentEndDateDisplay + '. Soll das Enddatum beim Setzen auf FINISHED auf den heutigen Tag ' + todayDisplay + ' gesetzt werden?';
                setModalState(finishedModal, true);
            }

            function closeModal(modal) {
                setModalState(modal, false);
                resetPendingCard();
            }

            function handleDrop(columnKey, card) {
                var targetStatusId = columnStatusMap[columnKey];
                if (!targetStatusId) {
                    return;
                }

                if (columnKey === 'blocked') {
                    openBlockedModal(card);

                    return;
                }

                if (columnKey === 'finished') {
                    openFinishedModal(card);

                    return;
                }

                submitCard(card, targetStatusId, 'keep');
            }

            function attachDragEvents(card) {
                card.addEventListener('dragstart', function () {
                    draggedCard = card;
                    card.classList.add('is-dragging');
                });

                card.addEventListener('dragend', function () {
                    card.classList.remove('is-dragging');
                    document.querySelectorAll('[data-project-column]').forEach(function (column) {
                        column.classList.remove('is-drop-target');
                    });
                    draggedCard = null;
                });
            }

            document.querySelectorAll('[data-project-card="true"]').forEach(attachDragEvents);

            document.querySelectorAll('[data-project-column]').forEach(function (column) {
                column.addEventListener('dragover', function (event) {
                    event.preventDefault();
                    column.classList.add('is-drop-target');
                });

                column.addEventListener('dragleave', function () {
                    column.classList.remove('is-drop-target');
                });

                column.addEventListener('drop', function (event) {
                    event.preventDefault();
                    column.classList.remove('is-drop-target');

                    if (!draggedCard) {
                        return;
                    }

                    var columnKey = column.getAttribute('data-project-column');
                    handleDrop(columnKey, draggedCard);
                });
            });

            if (blockedConfirmButton && blockedSelect) {
                blockedConfirmButton.addEventListener('click', function () {
                    if (!pendingCard) {
                        return;
                    }

                    setModalState(blockedModal, false);
                    submitCard(pendingCard, blockedSelect.value, 'keep');
                    resetPendingCard();
                });
            }

            if (finishedConfirmButton) {
                finishedConfirmButton.addEventListener('click', function () {
                    if (!pendingCard) {
                        return;
                    }

                    setModalState(finishedModal, false);
                    submitCard(pendingCard, columnStatusMap.finished, 'set_today');
                    resetPendingCard();
                });
            }

            if (finishedKeepButton) {
                finishedKeepButton.addEventListener('click', function () {
                    if (!pendingCard) {
                        return;
                    }

                    setModalState(finishedModal, false);
                    submitCard(pendingCard, columnStatusMap.finished, 'keep');
                    resetPendingCard();
                });
            }

            [blockedModal, finishedModal].forEach(function (modal) {
                if (!modal) {
                    return;
                }

                modal.querySelectorAll('[data-modal-close]').forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        closeModal(modal);
                    });
                });

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal(modal);
                    }
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') {
                    return;
                }

                if (blockedModal && blockedModal.getAttribute('aria-hidden') === 'false') {
                    closeModal(blockedModal);
                }

                if (finishedModal && finishedModal.getAttribute('aria-hidden') === 'false') {
                    closeModal(finishedModal);
                }
            });
        })();
    </script>
@endsection
