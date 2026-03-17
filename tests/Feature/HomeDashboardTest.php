<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_handles_customers_without_city(): void
    {
        $user = User::factory()->create();

        Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 9999,
            'sap_no' => '123456',
            'dynamics_no' => 'x',
            'name' => 'Ohne Ort',
            'city_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('index'))
            ->assertOk()
            ->assertSee('Ohne Ort')
            ->assertSee('Kein Ort');
    }
}
