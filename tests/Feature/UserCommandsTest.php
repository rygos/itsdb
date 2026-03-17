<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_view_lists_users(): void
    {
        User::factory()->create([
            'id' => 1,
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'permission_administration' => User::PERMISSION_ADMINISTRATION,
        ]);

        User::factory()->create([
            'id' => 2,
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'permission_administration' => User::PERMISSION_VISIBLE,
        ]);

        $this->artisan('users:view')
            ->expectsTable(
                [
                    'ID',
                    'Username',
                    'E-Mail',
                    'Administration',
                    'Produktematrix',
                    'Compose',
                    'Stunden',
                    'Customers',
                    'Projekte',
                    'Calender',
                ],
                [
                    [1, 'Alice', 'alice@example.com', 'Administration', 'Administration', 'Administration', 'Administration', 'Administration', 'Administration', 'Administration'],
                    [2, 'Bob', 'bob@example.com', 'Sichtbar', 'Administration', 'Administration', 'Administration', 'Administration', 'Administration', 'Administration'],
                ]
            )
            ->assertSuccessful();
    }

    public function test_users_makeadmin_sets_user_as_admin(): void
    {
        $user = User::factory()->create([
            'permission_administration' => User::PERMISSION_NONE,
        ]);

        $this->artisan('users:makeadmin', ['user_id' => $user->id])
            ->expectsOutput("Benutzer {$user->name} (ID {$user->id}) kann jetzt in der Benutzerverwaltung Rechte aendern.")
            ->assertSuccessful();

        $this->assertSame(User::PERMISSION_ADMINISTRATION, $user->fresh()->permission_administration);
    }
}
