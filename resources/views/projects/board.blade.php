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
            border: 1px solid rgba(0, 0, 0, 0.25);
            padding: 12px;
        }
        .project-board__column--new {
            background: #f4d35e;
            color: #2b2110;
        }
        .project-board__column--in-progress {
            background: #ee964b;
            color: #2d1605;
        }
        .project-board__column--blocked {
            background: #c44536;
            color: #fff4f2;
        }
        .project-board__column--finished {
            background: #4f772d;
            color: #f5ffe8;
        }
        .project-board__header {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.2);
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
            border: 1px solid rgba(0, 0, 0, 0.3);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            padding: 10px;
        }
        .project-card__title {
            font-weight: bold;
            margin-bottom: 6px;
        }
        .project-card__line {
            font-size: 12px;
            margin-bottom: 4px;
        }
        .project-card__status {
            display: inline-block;
            padding: 2px 6px;
            margin-bottom: 8px;
        }
        .project-card__form {
            margin-top: 8px;
        }
        .project-card select,
        .project-card input,
        .project-card button {
            width: 100%;
            color: #000;
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
                                        @php
                                            $statusName = optional($project->status)->name;
                                            $statusColor = \App\Helpers\StatusHelper::color($statusName);
                                            $statusTextColor = \App\Helpers\StatusHelper::textColor($statusName);
                                            $cardBackground = $statusColor === 'none' ? 'rgba(255, 255, 255, 0.85)' : $statusColor;
                                            $cardTextColor = $statusTextColor === 'inherit' ? '#000000' : $statusTextColor;
                                        @endphp
                                        <article class="project-card" style="background-color: {{ $cardBackground }}; color: {{ $cardTextColor }};">
                                            <div class="project-card__title">
                                                <a href="{{ route('projects.view', $project) }}" style="color: inherit;">{{ $project->name }}</a>
                                            </div>
                                            <div
                                                class="project-card__status"
                                                style="background-color: rgba(255, 255, 255, 0.25); color: inherit; border: 1px solid rgba(0, 0, 0, 0.15);"
                                            >
                                                {{ $statusName ?? 'Ohne Status' }}
                                            </div>
                                            <div class="project-card__line"><strong>Dynamics:</strong> {{ $project->dynamics_id }}</div>
                                            <div class="project-card__line">
                                                <strong>Kunde:</strong>
                                                @if($project->customer)
                                                    <a href="{{ route('customers.view', $project->customer) }}" style="color: inherit;">{{ $project->customer->name }}</a>
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
                                        <div class="project-card" style="background-color: rgba(255, 255, 255, 0.25); color: inherit;">Keine Projekte.</div>
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
