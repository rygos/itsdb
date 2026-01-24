<?php

namespace App\Helpers;

class StatusHelper
{
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
