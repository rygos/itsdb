<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomersViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_index_handles_customers_without_city(): void
    {
        $user = User::factory()->create();

        Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 1234,
            'sap_no' => '55555',
            'dynamics_no' => 'x',
            'name' => 'Null City Customer',
            'city_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Null City Customer')
            ->assertSee('Kein Ort');
    }
}
