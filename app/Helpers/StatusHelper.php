<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * @return array{background: string, border: string, color: string}
     */
    public static function accent(?string $status): array
    {
        return match (self::pipelineColumn($status)) {
            'new' => [
                'background' => 'rgba(77, 109, 140, 0.22)',
                'border' => '#4d6d8c',
                'color' => '#d9ecff',
            ],
            'blocked' => [
                'background' => 'rgba(143, 59, 82, 0.25)',
                'border' => '#8f3b52',
                'color' => '#ffd8e2',
            ],
            'finished' => [
                'background' => 'rgba(47, 122, 87, 0.24)',
                'border' => '#2f7a57',
                'color' => '#dcffe8',
            ],
            default => [
                'background' => 'rgba(47, 111, 159, 0.22)',
                'border' => '#2f6f9f',
                'color' => '#d7eeff',
            ],
        };
    }

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
        return self::accent($status)['background'];
    }

    public static function textColor(?string $status): string
    {
        return self::accent($status)['color'];
    }

    public static function borderColor(?string $status): string
    {
        return self::accent($status)['border'];
    }
}
