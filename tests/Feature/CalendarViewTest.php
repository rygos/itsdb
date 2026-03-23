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
                'type' => 'krankheit',
                'start_date' => '2026-04-03',
                'end_date' => '2026-04-07',
                'start_day_portion' => 'full',
                'end_day_portion' => 'full',
            ])
            ->assertRedirect(route('calendar.index', ['year' => '2026', 'month' => '04']));

        $vacation = Vacation::query()->firstOrFail();
        $this->assertSame($user->id, $vacation->user_id);
        $this->assertSame('krankheit', $vacation->type);
        $this->assertSame('2026-04-03', $vacation->start_date->toDateString());
        $this->assertSame('2026-04-07', $vacation->end_date->toDateString());
        $this->assertSame(2, $vacation->day_units);
        $this->assertSame('1', $vacation->display_days);

        $this->actingAs($user)
            ->get(route('calendar.index', ['year' => 2026, 'month' => '04']))
            ->assertOk()
            ->assertSee('Urlaub 2026')
            ->assertSee('Krankheit')
            ->assertSee('2026-04-03')
            ->assertSee('2026-04-07')
            ->assertDontSee('Keine Abwesenheiten fuer dieses Jahr vorhanden.');

        $this->actingAs($user)
            ->post(route('calendar.vacations.update', $vacation), [
                'type' => 'ueberstundenfrei',
                'start_date' => '2026-12-28',
                'end_date' => '2026-12-28',
                'start_day_portion' => 'half',
                'end_day_portion' => 'full',
            ])
            ->assertRedirect(route('calendar.index', ['year' => '2026', 'month' => '12']));

        $vacation->refresh();
        $this->assertSame('ueberstundenfrei', $vacation->type);
        $this->assertSame('2026-12-28', $vacation->start_date->toDateString());
        $this->assertSame('2026-12-28', $vacation->end_date->toDateString());
        $this->assertSame('half', $vacation->start_day_portion);
        $this->assertSame('full', $vacation->end_day_portion);
        $this->assertSame(1, $vacation->day_units);
        $this->assertSame('0.5', $vacation->display_days);

        $this->actingAs($user)
            ->post(route('calendar.vacations.delete', $vacation))
            ->assertRedirect(route('calendar.index', ['year' => '2026', 'month' => '12']));

        $this->assertDatabaseMissing('vacations', [
            'id' => $vacation->id,
        ]);
    }
}
