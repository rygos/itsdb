<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Customer;
use App\Models\OperatingSystem;
use App\Models\Server;
use App\Models\ServerKind;
use App\Models\User;
use App\Http\Middleware\VerifyCsrfToken;
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

    public function test_customer_view_shows_edit_link_for_editable_users(): void
    {
        $user = User::factory()->create();
        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 6072,
            'sap_no' => '3031303',
            'dynamics_no' => 'dyn',
            'name' => 'Mavie Med Holding GmbH',
            'city_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('customers.view', $customer))
            ->assertOk()
            ->assertSee(route('customers.edit', $customer), false)
            ->assertSee('Bearbeiten');
    }

    public function test_editable_user_can_update_customer_master_data(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $oldCity = City::query()->create([
            'name' => 'Wien',
            'country_code' => 'at',
        ]);
        $newCity = City::query()->create([
            'name' => 'Graz',
            'country_code' => 'at',
        ]);
        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 6072,
            'sap_no' => '3031303',
            'dynamics_no' => 'dyn',
            'name' => 'Mavie Med Holding GmbH',
            'city_id' => $oldCity->id,
        ]);

        $this->actingAs($user)
            ->post(route('customers.update', $customer), [
                'short_no' => 7001,
                'sap_no' => '9999999',
                'name' => 'Neuer Kundenname GmbH',
                'city' => (string) $newCity->id,
            ])
            ->assertRedirect(route('customers.view', $customer));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'short_no' => 7001,
            'sap_no' => '9999999',
            'name' => 'Neuer Kundenname GmbH',
            'city_id' => $newCity->id,
        ]);
    }

    public function test_customer_view_shows_server_kind_and_operating_system(): void
    {
        $user = User::factory()->create();
        $serverKind = ServerKind::query()->create([
            'name' => 'Applikationsserver',
        ]);
        $operatingSystem = OperatingSystem::query()->create([
            'name' => 'Windows Server 2022',
        ]);
        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 6072,
            'sap_no' => '3031303',
            'dynamics_no' => 'dyn',
            'name' => 'Mavie Med Holding GmbH',
            'city_id' => null,
        ]);
        Server::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'type' => 'Produktiv',
            'server_kind_id' => $serverKind->id,
            'operating_system_id' => $operatingSystem->id,
            'servername' => 'srv-app-01',
        ]);

        $this->actingAs($user)
            ->get(route('customers.view', $customer))
            ->assertOk()
            ->assertSee('Serverart')
            ->assertSee('OS')
            ->assertSee('Applikationsserver')
            ->assertSee('Windows Server 2022');
    }

    public function test_customer_view_shows_clipboard_image_upload_ui_for_documents(): void
    {
        $user = User::factory()->create();
        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'short_no' => 6072,
            'sap_no' => '3031303',
            'dynamics_no' => 'dyn',
            'name' => 'Mavie Med Holding GmbH',
            'city_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('customers.view', $customer))
            ->assertOk()
            ->assertSee('customer-document-paste-box')
            ->assertSee('Bild aus Zwischenablage hier einfuegen mit Strg+V')
            ->assertSee('customer-document-input');
    }
}
