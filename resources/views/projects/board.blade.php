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
                                                <input type="hidden" name="finished_end_date_action" value="keep">
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
    <script>
        (function () {
            var board = document.querySelector('.project-board');
            if (!board) {
                return;
            }

            var draggedCard = null;
            var columnStatusMap = @json($boardDropStatuses);

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
                    var targetStatusId = columnStatusMap[columnKey];
                    if (!targetStatusId) {
                        return;
                    }

                    var form = draggedCard.querySelector('.project-card__form');
                    var statusInput = draggedCard.querySelector('[data-project-status-input="true"]');
                    if (!form || !statusInput) {
                        return;
                    }

                    statusInput.value = targetStatusId;
                    form.submit();
                });
            });
        })();
    </script>
@endsection
