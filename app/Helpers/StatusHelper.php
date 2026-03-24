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
                'background' => 'rgba(77, 109, 140, 0.36)',
                'border' => '#6f93b6',
                'color' => '#eef7ff',
            ],
            'blocked' => [
                'background' => 'rgba(143, 59, 82, 0.38)',
                'border' => '#b85c79',
                'color' => '#ffe8ef',
            ],
            'finished' => [
                'background' => 'rgba(47, 122, 87, 0.36)',
                'border' => '#4da77b',
                'color' => '#ecfff2',
            ],
            default => [
                'background' => 'rgba(47, 111, 159, 0.36)',
                'border' => '#5f95bf',
                'color' => '#eef8ff',
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

    /**
     * @return array{background: string, border: string, color: string}
     */
    public static function deadlineAccent(int $daysRemaining): array
    {
        if ($daysRemaining > 0) {
            return [
                'background' => '#355f4e',
                'border' => '#4da77b',
                'color' => '#f3fff7',
            ];
        }

        if ($daysRemaining === 0) {
            return [
                'background' => '#8a6a12',
                'border' => '#d4a62a',
                'color' => '#fff8df',
            ];
        }

        return [
            'background' => '#7b3248',
            'border' => '#b85c79',
            'color' => '#fff0f4',
        ];
    }
}
