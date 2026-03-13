<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationToggleTest extends TestCase
{
    use RefreshDatabase;

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
}
