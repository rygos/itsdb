<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use App\Models\Vacation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_calendar_page_supports_vacation_crud_with_business_day_calculation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('calendar.vacations.store'), [
                'start_date' => '2026-04-03',
                'end_date' => '2026-04-07',
            ])
            ->assertRedirect(route('calendar.index', ['year' => '2026', 'month' => '04']));

        $vacation = Vacation::query()->firstOrFail();
        $this->assertSame($user->id, $vacation->user_id);
        $this->assertSame('2026-04-03', $vacation->start_date->toDateString());
        $this->assertSame('2026-04-07', $vacation->end_date->toDateString());
        $this->assertSame(1, $vacation->days);

        $this->actingAs($user)
            ->get(route('calendar.index', ['year' => 2026, 'month' => '04']))
            ->assertOk()
            ->assertSee('Urlaub 2026')
            ->assertSee('2026-04-03')
            ->assertSee('2026-04-07')
            ->assertDontSee('Keine Urlaube fuer dieses Jahr vorhanden.');

        $this->actingAs($user)
            ->post(route('calendar.vacations.update', $vacation), [
                'start_date' => '2026-12-24',
                'end_date' => '2026-12-28',
            ])
            ->assertRedirect(route('calendar.index', ['year' => '2026', 'month' => '12']));

        $vacation->refresh();
        $this->assertSame('2026-12-24', $vacation->start_date->toDateString());
        $this->assertSame('2026-12-28', $vacation->end_date->toDateString());
        $this->assertSame(2, $vacation->days);

        $this->actingAs($user)
            ->post(route('calendar.vacations.delete', $vacation))
            ->assertRedirect(route('calendar.index', ['year' => '2026', 'month' => '12']));

        $this->assertDatabaseMissing('vacations', [
            'id' => $vacation->id,
        ]);
    }
}
