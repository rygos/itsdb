<?php

namespace App\Helpers;

class StatusHelper
{
    public static function pipelineColumns(): array
    {
        return [
            'new' => 'Neu',
            'in_progress' => 'In Arbeit',
            'blocked' => 'Blockiert',
            'finished' => 'Fertig',
        ];
    }

    public static function pipelineColumn(?string $status): string
    {
        $normalizedStatus = strtoupper(trim((string) $status));

        return match ($normalizedStatus) {
            '', 'NEW', 'OPEN', 'BACKLOG', 'TODO' => 'new',
            'ON HOLD', 'WAIT FOR INFO', 'BLOCKED', 'BLOCKIERT' => 'blocked',
            'FINISHED', 'DONE', 'CLOSED', 'ERLEDIGT' => 'finished',
            default => 'in_progress',
        };
    }

    public static function color(?string $status): string
    {
        switch ($status) {
            case 'WIP':
                return 'orange';
            case 'CHECK':
                return 'blue';
            case 'ON HOLD':
                return 'red';
            case 'WAIT FOR INFO':
                return 'yellow';
            case 'FINISHED':
                return 'green';
            case 'NEW':
            default:
                return 'none';
        }
    }

    public static function textColor(?string $status): string
    {
        switch ($status) {
            case 'CHECK':
            case 'ON HOLD':
            case 'FINISHED':
                return 'white';
            case 'WIP':
            case 'WAIT FOR INFO':
                return 'black';
            case 'NEW':
            default:
                return 'inherit';
        }
    }
}
