<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\AppSetting;
use App\Models\OperatingSystem;
use App\Models\ServerKind;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_administration_page_requires_administration_visibility(): void
    {
        $admin = User::factory()->create();
        $restrictedUser = User::factory()->create([
            'permission_administration' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('administration.index'))
            ->assertOk()
            ->assertSee('Benutzerverwaltung');

        $this->actingAs($restrictedUser)
            ->get(route('administration.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_update_user_profile_and_permissions(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create([
            'permission_administration' => 0,
            'permission_product_matrix' => 1,
            'permission_compose' => 1,
            'permission_hours' => 1,
            'permission_customers' => 1,
            'permission_projects' => 1,
            'permission_calendar' => 1,
        ]);

        $response = $this->actingAs($admin)->post(route('administration.users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'permissions' => [
                'administration' => [1, 2, 3],
                'product_matrix' => [1, 2],
                'compose' => [1],
                'hours' => [],
                'customers' => [1, 2],
                'projects' => [1, 2, 3],
                'calendar' => [1],
            ],
        ]);

        $response->assertRedirect(route('administration.index', ['tab' => 'users']));

        $user->refresh();

        $this->assertSame('Updated User', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertSame(3, $user->permission_administration);
        $this->assertSame(2, $user->permission_product_matrix);
        $this->assertSame(1, $user->permission_compose);
        $this->assertSame(0, $user->permission_hours);
        $this->assertSame(2, $user->permission_customers);
        $this->assertSame(3, $user->permission_projects);
        $this->assertSame(1, $user->permission_calendar);
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_statuses_can_be_created_and_updated_from_administration(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('administration.statuses.store'), [
                'name' => 'PLANNED',
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'statuses']));

        $status = Status::query()->where('name', 'PLANNED')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('administration.statuses.update', $status), [
                'name' => 'IN REVIEW',
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'statuses']));

        $this->assertDatabaseHas('status', [
            'id' => $status->id,
            'name' => 'IN REVIEW',
        ]);
    }

    public function test_server_master_data_can_be_created_and_updated_from_administration(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('administration.server_kinds.store'), [
                'name' => 'Applikationsserver',
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'master-data']));

        $serverKind = ServerKind::query()->where('name', 'Applikationsserver')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('administration.server_kinds.update', $serverKind), [
                'name' => 'Datenbankserver',
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'master-data']));

        $this->actingAs($admin)
            ->post(route('administration.operating_systems.store'), [
                'name' => 'Windows Server 2022',
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'master-data']));

        $operatingSystem = OperatingSystem::query()->where('name', 'Windows Server 2022')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('administration.operating_systems.update', $operatingSystem), [
                'name' => 'Ubuntu 24.04',
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'master-data']));

        $this->assertDatabaseHas('server_kinds', [
            'id' => $serverKind->id,
            'name' => 'Datenbankserver',
        ]);

        $this->assertDatabaseHas('operating_systems', [
            'id' => $operatingSystem->id,
            'name' => 'Ubuntu 24.04',
        ]);
    }

    public function test_registration_setting_can_be_toggled_in_administration(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('administration.settings.update'), [
                'registration_enabled' => 1,
            ])
            ->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'settings']));

        $this->assertTrue(AppSetting::getBoolean('registration_enabled'));
        auth()->logout();
        $this->get(route('register'))->assertOk();

        $this->actingAs($admin)
            ->post(route('administration.settings.update'), [])
            ->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'settings']));

        $this->assertFalse(AppSetting::getBoolean('registration_enabled'));
        auth()->logout();
        $this->get(route('register'))->assertNotFound();
    }
}
