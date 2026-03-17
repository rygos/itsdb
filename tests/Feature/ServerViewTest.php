<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Customer;
use App\Models\OperatingSystem;
use App\Models\Server;
use App\Models\ServerKind;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_server_view_shows_server_kind_and_operating_system_fields(): void
    {
        $user = User::factory()->create();
        $serverKind = ServerKind::query()->create([
            'name' => 'Applikationsserver',
        ]);
        $operatingSystem = OperatingSystem::query()->create([
            'name' => 'Ubuntu 24.04',
        ]);
        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 1234,
            'sap_no' => '55555',
            'dynamics_no' => 'x',
            'name' => 'Serverkunde',
            'city_id' => null,
        ]);
        $server = Server::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'type' => 'Produktiv',
            'server_kind_id' => $serverKind->id,
            'operating_system_id' => $operatingSystem->id,
            'servername' => 'srv-app-01',
        ]);

        $this->actingAs($user)
            ->get(route('servers.view', $server))
            ->assertOk()
            ->assertSee('Serverart')
            ->assertSee('OS')
            ->assertSee('Applikationsserver')
            ->assertSee('Ubuntu 24.04');
    }

    public function test_server_view_can_update_server_kind_and_operating_system(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $serverKind = ServerKind::query()->create([
            'name' => 'Applikationsserver',
        ]);
        $newServerKind = ServerKind::query()->create([
            'name' => 'Datenbankserver',
        ]);
        $operatingSystem = OperatingSystem::query()->create([
            'name' => 'Ubuntu 24.04',
        ]);
        $newOperatingSystem = OperatingSystem::query()->create([
            'name' => 'Windows Server 2022',
        ]);
        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 1234,
            'sap_no' => '55555',
            'dynamics_no' => 'x',
            'name' => 'Serverkunde',
            'city_id' => null,
        ]);
        $server = Server::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'type' => 'Produktiv',
            'server_kind_id' => $serverKind->id,
            'operating_system_id' => $operatingSystem->id,
            'servername' => 'srv-app-01',
            'fqdn' => 'srv-app-01.local',
        ]);

        $this->actingAs($user)
            ->post(route('servers.update'), [
                'server_id' => $server->id,
                'type' => 'Test',
                'server_kind_id' => (string) $newServerKind->id,
                'operating_system_id' => (string) $newOperatingSystem->id,
                'servername' => 'srv-db-01',
                'fqdn' => 'srv-db-01.local',
                'db_sid' => 'ABC',
                'db_server' => 'db01',
                'ext_ip' => '1.2.3.4',
                'int_ip' => '10.0.0.4',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('servers', [
            'id' => $server->id,
            'type' => 'Test',
            'server_kind_id' => $newServerKind->id,
            'operating_system_id' => $newOperatingSystem->id,
            'servername' => 'srv-db-01',
            'fqdn' => 'srv-db-01.local',
        ]);
    }
}
