@extends('layouts.app')
@section('title', 'Projekt-Board')
@section('content')
    <style>
        .project-board {
            display: grid;
            grid-template-columns: repeat(4, minmax(220px, 1fr));
            gap: 16px;
            align-items: start;
        }
        .project-board__column {
            background: #00001a;
            color: #ffffe0;
            border: 1px solid #17395c;
            padding: 12px;
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
        }
        .project-card {
            background: #0a1630;
            color: #ffffe0;
            border: 1px solid #17395c;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 224, 0.06);
            padding: 10px;
        }
        .project-card__title {
            font-weight: bold;
            margin-bottom: 6px;
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
            background: #17395c;
            color: #ffffe0;
            border: 1px solid #295785;
        }
        .project-card__form {
            margin-top: 8px;
        }
        .project-card select,
        .project-card input,
        .project-card button {
            width: 100%;
            color: #ffffe0;
        }
        .project-card select,
        .project-card input {
            background: #00001a;
            border: 1px solid #17395c;
        }
        .project-card button {
            background: #17395c;
            border: 1px solid #295785;
        }
        .project-card button:hover {
            background: #295785;
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
    <div id="prodpagecontainer">
        <table id="pouetbox_prodmain">
            <thead>
            <tr id="prodheader">
                <th>
                    <span id="title"><big>Projekt-Pipeline</big></span>
                    <div id="nfo">Kanban-Ansicht fuer deine Projekte nach Statusgruppe</div>
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
                                <div class="project-board__stack">
                                    @forelse($column['projects'] as $project)
                                        @php($statusName = optional($project->status)->name)
                                        <article class="project-card">
                                            <div class="project-card__title">
                                                <a href="{{ route('projects.view', $project) }}">{{ $project->name }}</a>
                                            </div>
                                            <div class="project-card__status">
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
                                            <form method="POST" action="{{ route('projects.change_status') }}" class="project-card__form">
                                                @csrf
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <input type="hidden" name="finished_end_date_action" value="keep">
                                                {{ html()->select('status', $statusOptions, $project->status_id) }}
                                                {{ html()->submit('Status setzen') }}
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
@endsection
