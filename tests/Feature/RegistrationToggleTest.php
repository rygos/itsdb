<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_registration_form_is_not_available_when_disabled(): void
    {
        config()->set('app.registration_enabled', false);

        $this->get(route('register'))->assertNotFound();
    }

    public function test_registration_is_blocked_when_disabled(): void
    {
        config()->set('app.registration_enabled', false);

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertForbidden();
    }

    public function test_registration_form_is_available_when_enabled(): void
    {
        config()->set('app.registration_enabled', true);

        $this->get(route('register'))->assertOk();
    }

    public function test_newly_registered_users_have_no_permissions(): void
    {
        config()->set('app.registration_enabled', true);

        $this->post(route('register'), [
            'name' => 'No Rights',
            'email' => 'norights@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('/');

        $user = User::query()->where('email', 'norights@example.com')->firstOrFail();

        $this->assertSame(User::PERMISSION_NONE, $user->permission_administration);
        $this->assertSame(User::PERMISSION_NONE, $user->permission_product_matrix);
        $this->assertSame(User::PERMISSION_NONE, $user->permission_compose);
        $this->assertSame(User::PERMISSION_NONE, $user->permission_hours);
        $this->assertSame(User::PERMISSION_NONE, $user->permission_customers);
        $this->assertSame(User::PERMISSION_NONE, $user->permission_projects);
        $this->assertSame(User::PERMISSION_NONE, $user->permission_calendar);
    }
}
