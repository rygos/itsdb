<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Log;
use App\Models\Project;
use App\Models\Server;
use App\Models\Status;
use App\Models\User;
use App\Models\Vacation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_statistics_page_shows_aggregated_user_data(): void
    {
        $viewer = User::factory()->create([
            'name' => 'Viewer User',
        ]);

        $finishedStatus = Status::query()->create([
            'name' => 'FINISHED',
        ]);

        $openStatus = Status::query()->create([
            'name' => 'OPEN',
        ]);

        $customer = Customer::query()->create([
            'user_id' => $viewer->id,
            'short_no' => 1001,
            'sap_no' => 'SAP-1001',
            'dynamics_no' => 'DYN-1001',
            'name' => 'Acme GmbH',
            'city_id' => null,
        ]);

        Project::query()->create([
            'dynamics_id' => 'PRJ-OPEN',
            'name' => 'Migration',
            'customer_id' => $customer->id,
            'user_id' => $viewer->id,
            'status_id' => $openStatus->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(10),
            'hours' => 7,
        ]);

        Project::query()->create([
            'dynamics_id' => 'PRJ-DONE',
            'name' => 'Go Live',
            'customer_id' => $customer->id,
            'user_id' => $viewer->id,
            'status_id' => $finishedStatus->id,
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(2),
            'hours' => 5,
        ]);

        Server::query()->create([
            'user_id' => $viewer->id,
            'customer_id' => $customer->id,
            'servername' => 'app01',
            'type' => 'Produktiv',
        ]);

        CustomerDocument::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $viewer->id,
            'original_name' => 'runbook.pdf',
            'stored_name' => 'stored-runbook.pdf',
            'disk' => 'local',
            'path' => 'docs/runbook.pdf',
            'description' => 'Runbook',
            'file_size' => 4096,
            'mime_type' => 'application/pdf',
        ]);

        Vacation::query()->create([
            'user_id' => $viewer->id,
            'type' => Vacation::TYPE_VACATION,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'start_day_portion' => Vacation::PORTION_FULL,
            'end_day_portion' => Vacation::PORTION_FULL,
            'days' => 2,
            'day_units' => 4,
        ]);

        Log::query()->create([
            'user_id' => $viewer->id,
            'section' => 'project',
            'type' => 'Update',
            'msg' => 'Projekt aktualisiert',
            'content_id' => 1,
        ]);

        $response = $this->actingAs($viewer)->get(route('users.statistics', ['user_id' => $viewer->id]));

        $response->assertOk()
            ->assertSee('User-Statistik fuer Viewer User')
            ->assertSee('Acme GmbH')
            ->assertSee('Migration')
            ->assertSee('Go Live')
            ->assertSee('app01')
            ->assertSee('runbook.pdf')
            ->assertSee('Projekt aktualisiert')
            ->assertSee('Abwesenheiten')
            ->assertSee('12')
            ->assertSee('4,00 KB');
    }

    public function test_non_administrators_cannot_view_other_user_statistics(): void
    {
        $viewer = User::factory()->create([
            'permission_administration' => 0,
        ]);

        $otherUser = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('users.statistics', ['user_id' => $otherUser->id]))
            ->assertForbidden();
    }
}
