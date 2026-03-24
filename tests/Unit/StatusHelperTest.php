<?php

namespace Tests\Unit;

use App\Helpers\StatusHelper;
use PHPUnit\Framework\TestCase;

class StatusHelperTest extends TestCase
{
    public function test_accent_palette_matches_project_board_colors(): void
    {
        $this->assertSame([
            'background' => 'rgba(77, 109, 140, 0.22)',
            'border' => '#4d6d8c',
            'color' => '#d9ecff',
        ], StatusHelper::accent('OPEN'));

        $this->assertSame([
            'background' => 'rgba(47, 111, 159, 0.22)',
            'border' => '#2f6f9f',
            'color' => '#d7eeff',
        ], StatusHelper::accent('WIP'));

        $this->assertSame([
            'background' => 'rgba(143, 59, 82, 0.25)',
            'border' => '#8f3b52',
            'color' => '#ffd8e2',
        ], StatusHelper::accent('WAIT FOR INFO'));

        $this->assertSame([
            'background' => 'rgba(47, 122, 87, 0.24)',
            'border' => '#2f7a57',
            'color' => '#dcffe8',
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
