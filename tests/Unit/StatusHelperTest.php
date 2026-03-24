<?php

namespace Tests\Unit;

use App\Helpers\StatusHelper;
use PHPUnit\Framework\TestCase;

class StatusHelperTest extends TestCase
{
    public function test_accent_palette_matches_project_board_colors(): void
    {
        $this->assertSame([
            'background' => 'rgba(77, 109, 140, 0.36)',
            'border' => '#6f93b6',
            'color' => '#eef7ff',
        ], StatusHelper::accent('OPEN'));

        $this->assertSame([
            'background' => 'rgba(47, 111, 159, 0.36)',
            'border' => '#5f95bf',
            'color' => '#eef8ff',
        ], StatusHelper::accent('WIP'));

        $this->assertSame([
            'background' => 'rgba(143, 59, 82, 0.38)',
            'border' => '#b85c79',
            'color' => '#ffe8ef',
        ], StatusHelper::accent('WAIT FOR INFO'));

        $this->assertSame([
            'background' => 'rgba(47, 122, 87, 0.36)',
            'border' => '#4da77b',
            'color' => '#ecfff2',
        ], StatusHelper::accent('FINISHED'));
    }

    public function test_color_helpers_use_shared_accent_palette(): void
    {
        $accent = StatusHelper::accent('CHECK');

        $this->assertSame($accent['background'], StatusHelper::color('CHECK'));
        $this->assertSame($accent['border'], StatusHelper::borderColor('CHECK'));
        $this->assertSame($accent['color'], StatusHelper::textColor('CHECK'));
    }
}
