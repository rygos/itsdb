<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
        Carbon::setTestNow('2026-03-24 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * The project board groups only the authenticated user's projects into the
     * expected Kanban columns.
     */
    public function test_project_board_groups_projects_into_pipeline_columns(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $newStatus = Status::query()->create(['name' => 'OPEN']);
        $wipStatus = Status::query()->create(['name' => 'WIP']);
        $blockedStatus = Status::query()->create(['name' => 'WAIT FOR INFO']);
        $finishedStatus = Status::query()->create(['name' => 'FINISHED']);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 7001,
            'sap_no' => 'SAP-7001',
            'dynamics_no' => 'DYN-7001',
            'name' => 'Board Customer',
            'city_id' => null,
        ]);

        $otherCustomer = Customer::query()->create([
            'user_id' => $otherUser->id,
            'short_no' => 7002,
            'sap_no' => 'SAP-7002',
            'dynamics_no' => 'DYN-7002',
            'name' => 'Foreign Customer',
            'city_id' => null,
        ]);

        Project::query()->create([
            'dynamics_id' => 'KAN-NEW',
            'name' => 'Neue Aufgabe',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'status_id' => $newStatus->id,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addDays(10),
            'hours' => 3,
        ]);

        Project::query()->create([
            'dynamics_id' => 'KAN-WIP',
            'name' => 'Laufende Aufgabe',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'status_id' => $wipStatus->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'hours' => 5,
        ]);

        Project::query()->create([
            'dynamics_id' => 'KAN-BLOCK',
            'name' => 'Blockierte Aufgabe',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'status_id' => $blockedStatus->id,
            'start_date' => now()->subDays(8),
            'end_date' => now()->addDays(3),
            'hours' => 2,
        ]);

        Project::query()->create([
            'dynamics_id' => 'KAN-DONE',
            'name' => 'Fertige Aufgabe',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'status_id' => $finishedStatus->id,
            'start_date' => now()->subDays(12),
            'end_date' => now()->subDay(),
            'hours' => 8,
        ]);

        Project::query()->create([
            'dynamics_id' => 'KAN-OLD-DONE',
            'name' => 'Alte fertige Aufgabe',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'status_id' => $finishedStatus->id,
            'start_date' => now()->subDays(70),
            'end_date' => now()->subDays(45),
            'hours' => 13,
        ]);

        Project::query()->create([
            'dynamics_id' => 'KAN-OTHER',
            'name' => 'Fremde Aufgabe',
            'customer_id' => $otherCustomer->id,
            'user_id' => $otherUser->id,
            'status_id' => $newStatus->id,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(2),
            'hours' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('projects.board'));

        $response->assertOk()
            ->assertSee('Projekt-Pipeline')
            ->assertSee('Neu')
            ->assertSee('In Arbeit')
            ->assertSee('Blockiert')
            ->assertSee('Fertig')
            ->assertSee('Neue Aufgabe')
            ->assertSee('Laufende Aufgabe')
            ->assertSee('Blockierte Aufgabe')
            ->assertSee('Fertige Aufgabe')
            ->assertSee('Nur Abschluesse der letzten 30 Tage')
            ->assertDontSee('Alte fertige Aufgabe')
            ->assertDontSee('Fremde Aufgabe');
    }
}
