<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use App\Http\Middleware\VerifyCsrfToken;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_hours_page_only_shows_finished_projects_of_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $finished = Status::create(['name' => 'FINISHED']);

        $ownCustomer = $this->createCustomer($user, '1001');
        $otherCustomer = $this->createCustomer($otherUser, '1002');

        Project::create([
            'dynamics_id' => 'OWN-1',
            'name' => 'Own finished project',
            'customer_id' => $ownCustomer->id,
            'user_id' => $user->id,
            'status_id' => $finished->id,
            'start_date' => '2026-01-01 00:00:00',
            'end_date' => '2026-01-15 00:00:00',
            'hours' => 8,
        ]);

        Project::create([
            'dynamics_id' => 'OTHER-1',
            'name' => 'Other finished project',
            'customer_id' => $otherCustomer->id,
            'user_id' => $otherUser->id,
            'status_id' => $finished->id,
            'start_date' => '2026-01-01 00:00:00',
            'end_date' => '2026-01-15 00:00:00',
            'hours' => 12,
        ]);

        $response = $this->actingAs($user)->get(route('hours.index', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee('Own finished project');
        $response->assertDontSee('Other finished project');
        $response->assertSee('Wochenende');
        $response->assertSee('Wochendurchschnitt');
    }

    public function test_calendar_page_only_counts_and_lists_projects_of_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $open = Status::create(['name' => 'OPEN']);

        $ownCustomer = $this->createCustomer($user, '2001');
        $otherCustomer = $this->createCustomer($otherUser, '2002');

        Project::create([
            'dynamics_id' => 'OWN-2',
            'name' => 'Own calendar project',
            'customer_id' => $ownCustomer->id,
            'user_id' => $user->id,
            'status_id' => $open->id,
            'start_date' => '2026-02-01 00:00:00',
            'end_date' => '2026-02-10 00:00:00',
            'hours' => 5,
        ]);

        Project::create([
            'dynamics_id' => 'OTHER-2',
            'name' => 'Other calendar project',
            'customer_id' => $otherCustomer->id,
            'user_id' => $otherUser->id,
            'status_id' => $open->id,
            'start_date' => '2026-02-01 00:00:00',
            'end_date' => '2026-02-10 00:00:00',
            'hours' => 7,
        ]);

        $response = $this->actingAs($user)->get(route('calendar.index', ['year' => 2026, 'month' => '02']).'?day=2026-02-10');

        $response->assertOk();
        $response->assertSee('Own calendar project');
        $response->assertDontSee('Other calendar project');
        $response->assertSee('(1)', false);
    }

    public function test_only_project_owner_can_change_status(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $open = Status::create(['name' => 'OPEN']);
        $finished = Status::create(['name' => 'FINISHED']);
        $customer = $this->createCustomer($owner, '3001');

        $project = Project::create([
            'dynamics_id' => 'OWN-3',
            'name' => 'Protected project',
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'status_id' => $open->id,
            'start_date' => '2026-03-01 00:00:00',
            'end_date' => '2026-03-02 00:00:00',
            'hours' => 4,
        ]);

        $response = $this->actingAs($otherUser)->post(route('projects.change_status'), [
            'project_id' => $project->id,
            'status' => $finished->id,
        ]);

        $response->assertForbidden();
        $this->assertSame($open->id, $project->fresh()->status_id);
    }

    public function test_only_project_owner_can_update_project_details(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $open = Status::create(['name' => 'OPEN']);
        $customer = $this->createCustomer($owner, '4001');

        $project = Project::create([
            'dynamics_id' => 'OWN-4',
            'name' => 'Editable project',
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'status_id' => $open->id,
            'start_date' => '2026-03-03 00:00:00',
            'end_date' => '2026-03-04 00:00:00',
            'hours' => 6,
        ]);

        $response = $this->actingAs($otherUser)->post(route('projects.update'), [
            'id' => $project->id,
            'name' => 'Hijacked project',
            'dynamics_id' => 'OWN-4',
            'start_date' => '2026-03-03',
            'end_date' => '2026-03-04',
            'hours' => 9,
        ]);

        $response->assertForbidden();
        $this->assertSame('Editable project', $project->fresh()->name);
        $this->assertSame(6, $project->fresh()->hours);
    }

    public function test_setting_finished_status_can_update_end_date_to_today(): void
    {
        Carbon::setTestNow('2026-03-18 09:30:00');

        $owner = User::factory()->create();
        $open = Status::create(['name' => 'OPEN']);
        $finished = Status::create(['name' => 'FINISHED']);
        $customer = $this->createCustomer($owner, '4501');

        $project = Project::create([
            'dynamics_id' => 'OWN-5',
            'name' => 'Finished today project',
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'status_id' => $open->id,
            'start_date' => '2026-03-01 00:00:00',
            'end_date' => '2026-03-10 00:00:00',
            'hours' => 6,
        ]);

        $this->actingAs($owner)
            ->from(route('customers.view', $customer))
            ->post(route('projects.change_status'), [
                'project_id' => $project->id,
                'status' => $finished->id,
                'finished_end_date_action' => 'set_today',
            ])
            ->assertRedirect(route('customers.view', $customer));

        $project->refresh();

        $this->assertSame($finished->id, $project->status_id);
        $this->assertSame('2026-03-18', $project->end_date->toDateString());
    }

    public function test_setting_finished_status_can_keep_existing_end_date(): void
    {
        Carbon::setTestNow('2026-03-18 09:30:00');

        $owner = User::factory()->create();
        $open = Status::create(['name' => 'OPEN']);
        $finished = Status::create(['name' => 'FINISHED']);
        $customer = $this->createCustomer($owner, '4502');

        $project = Project::create([
            'dynamics_id' => 'OWN-6',
            'name' => 'Keep original end date project',
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'status_id' => $open->id,
            'start_date' => '2026-03-01 00:00:00',
            'end_date' => '2026-03-10 00:00:00',
            'hours' => 6,
        ]);

        $this->actingAs($owner)
            ->from(route('customers.view', $customer))
            ->post(route('projects.change_status'), [
                'project_id' => $project->id,
                'status' => $finished->id,
                'finished_end_date_action' => 'keep',
            ])
            ->assertRedirect(route('customers.view', $customer));

        $project->refresh();

        $this->assertSame($finished->id, $project->status_id);
        $this->assertSame('2026-03-10', $project->end_date->toDateString());
    }

    public function test_project_store_validates_payload_and_assigns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $customer = $this->createCustomer($user, '5001');

        $this->actingAs($user)
            ->post(route('projects.store'), [
                'dynamics_id' => 'PROJ-5001',
                'name' => 'Validated project',
                'customer' => $customer->id,
                'start_date' => '2026-04-01',
                'end_date' => '2026-04-05',
                'hours' => 10,
            ])
            ->assertRedirect(route('index'));

        $this->assertDatabaseHas('projects', [
            'dynamics_id' => 'PROJ-5001',
            'name' => 'Validated project',
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'hours' => 10,
        ]);
    }

    private function createCustomer(User $user, string $shortNo): Customer
    {
        $city = City::create([
            'name' => 'Berlin '.$shortNo,
            'country_code' => 'de',
        ]);

        return Customer::create([
            'user_id' => $user->id,
            'short_no' => (int) $shortNo,
            'sap_no' => 'SAP-'.$shortNo,
            'dynamics_no' => 'DYN-'.$shortNo,
            'name' => 'Customer '.$shortNo,
            'city_id' => $city->id,
        ]);
    }
}
