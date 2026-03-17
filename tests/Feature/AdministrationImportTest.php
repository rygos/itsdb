<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\City;
use App\Models\Customer;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdministrationImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_customer_import_creates_customers_and_cities(): void
    {
        $admin = User::factory()->create();

        $csv = <<<'CSV'
Kd.Nummer;SAP-Nr.;Ort;AMS
2140;3022664;Berlin;ja
2161;2002680;Kaiserslautern;ja
CSV;

        $this->actingAs($admin)->post(route('administration.imports.customers'), [
            'fallback_country_code' => 'de',
            'csv_file' => UploadedFile::fake()->createWithContent('kunden.csv', $csv),
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseHas('customers', [
            'short_no' => 2140,
            'sap_no' => '3022664',
            'dynamics_no' => 'x',
            'name' => 'Unbekannt',
        ]);

        $this->assertDatabaseHas('citys', [
            'name' => 'Berlin',
            'country_code' => 'de',
        ]);
    }

    public function test_customer_import_does_not_change_existing_customers_and_creates_no_duplicates(): void
    {
        $admin = User::factory()->create();
        $existing = Customer::query()->create([
            'user_id' => $admin->id,
            'short_no' => 2140,
            'sap_no' => 'OLD-SAP',
            'dynamics_no' => 'old',
            'name' => 'Bestehend',
        ]);

        $csv = <<<'CSV'
Kd.Nummer;SAP-Nr.;Ort;AMS
2140;3022664;Berlin;ja
2140;9999999;Hamburg;ja
2161;2002680;Kaiserslautern;ja
2161;2002681;Koeln;ja
CSV;

        $this->actingAs($admin)->post(route('administration.imports.customers'), [
            'fallback_country_code' => 'de',
            'csv_file' => UploadedFile::fake()->createWithContent('kunden.csv', $csv),
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseHas('customers', [
            'id' => $existing->id,
            'short_no' => 2140,
            'sap_no' => 'OLD-SAP',
            'dynamics_no' => 'old',
            'name' => 'Bestehend',
        ]);

        $this->assertSame(2, Customer::query()->count());
        $this->assertSame(1, Customer::query()->where('short_no', 2161)->count());
    }

    public function test_orbisu_server_import_updates_customer_and_server_data(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::query()->create([
            'user_id' => $admin->id,
            'short_no' => 1006,
            'sap_no' => '',
            'dynamics_no' => 'x',
            'name' => 'Unbekannt',
        ]);

        $server = Server::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'servername' => 'sb-lxorbisu-01',
            'type' => 'Test',
            'int_ip' => '1.1.1.1',
            'db_sid' => 'OLD',
        ]);
        $server->updated_at = now()->subYears(2);
        $server->save();

        $csv = <<<'CSV'
Kunde;Kundename (SAP-Nr.);;Kundenprojekt;Umgebung;Status Installation;OK;Fehler;VM konfiguriert;Funktionen;ASSM-Ticket;VM-Hostname;VM-IP-Addresse;DB-SID;Erstellt;Erstellt von;Aktualisiert;Aktualisiert von;OUC-ID
1006 Saarbrücken (NonAMS);SHG-Kliniken Sonnenberg (3031036);75400631;Saarbrücken IRIS OASe + Docker;1. Produktiv;Docker-Container installiert;ja;keine Fehler;;; ;sb-lxorbisu-01;10.20.5.100;KHVS;2021.10;Ralf;2025.11;Bernd;92148727
CSV;

        $this->actingAs($admin)->post(route('administration.imports.orbisu_servers'), [
            'csv_file' => UploadedFile::fake()->createWithContent('servers.csv', $csv),
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'sap_no' => '3031036',
            'name' => 'SHG-Kliniken Sonnenberg',
        ]);

        $this->assertDatabaseHas('servers', [
            'id' => $server->id,
            'customer_id' => $customer->id,
            'servername' => 'sb-lxorbisu-01',
            'type' => 'Produktiv',
            'int_ip' => '10.20.5.100',
            'db_sid' => 'KHVS',
        ]);
    }

    public function test_orbisu_server_import_skips_existing_server_when_database_is_newer(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::query()->create([
            'user_id' => $admin->id,
            'short_no' => 1006,
            'sap_no' => '',
            'dynamics_no' => 'x',
            'name' => 'Unbekannt',
        ]);

        $server = Server::query()->create([
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'servername' => 'sb-lxorbisu-01',
            'type' => 'Test',
            'int_ip' => '1.1.1.1',
            'db_sid' => 'OLD',
        ]);
        $server->updated_at = now()->addDay();
        $server->save();

        $csv = <<<'CSV'
Kunde;Kundename (SAP-Nr.);;Kundenprojekt;Umgebung;Status Installation;OK;Fehler;VM konfiguriert;Funktionen;ASSM-Ticket;VM-Hostname;VM-IP-Addresse;DB-SID;Erstellt;Erstellt von;Aktualisiert;Aktualisiert von;OUC-ID
1006 Saarbrücken (NonAMS);SHG-Kliniken Sonnenberg (3031036);75400631;Saarbrücken IRIS OASe + Docker;1. Produktiv;Docker-Container installiert;ja;keine Fehler;;; ;sb-lxorbisu-01;10.20.5.100;KHVS;2021.10;Ralf;2025.11;Bernd;92148727
CSV;

        $this->actingAs($admin)->post(route('administration.imports.orbisu_servers'), [
            'csv_file' => UploadedFile::fake()->createWithContent('servers.csv', $csv),
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseHas('servers', [
            'id' => $server->id,
            'type' => 'Test',
            'int_ip' => '1.1.1.1',
            'db_sid' => 'OLD',
        ]);
    }
}
