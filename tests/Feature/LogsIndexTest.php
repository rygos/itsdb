<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_page_lists_customer_logs_with_customer_context(): void
    {
        $user = User::factory()->create([
            'name' => 'Log User',
        ]);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 1234,
            'sap_no' => 'SAP-1234',
            'dynamics_no' => 'DYN-1234',
            'name' => 'Log Customer GmbH',
            'city_id' => null,
        ]);

        Log::query()->create([
            'user_id' => $user->id,
            'section' => 'customer',
            'type' => 'Update',
            'msg' => 'Customer updated.',
            'content_id' => $customer->id,
        ]);

        Log::query()->create([
            'user_id' => $user->id,
            'section' => 'project',
            'type' => 'Update',
            'msg' => 'Project updated.',
            'content_id' => 999,
        ]);

        $this->actingAs($user)
            ->get(route('logs.index'))
            ->assertOk()
            ->assertSee('Log User')
            ->assertSee('1234')
            ->assertSee('SAP-1234')
            ->assertSee('Log Customer GmbH')
            ->assertSee('Customer updated.')
            ->assertDontSee('Project updated.')
            ->assertSee(route('customers.view', $customer), false);
    }
}
