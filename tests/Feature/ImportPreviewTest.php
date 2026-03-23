<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Container;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_customer_import_supports_preview_before_confirm(): void
    {
        $admin = User::factory()->create();
        $csv = <<<'CSV'
Kd.Nummer;SAP-Nr.;Ort;AMS
2140;3022664;Berlin;ja
CSV;

        $this->actingAs($admin)->post(route('administration.imports.customers'), [
            'import_mode' => 'preview',
            'fallback_country_code' => 'de',
            'csv_file' => UploadedFile::fake()->createWithContent('kunden.csv', $csv),
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseCount('customers', 0);
        $preview = session('import_preview');
        $this->assertSame('customers', $preview['type']);

        $this->actingAs($admin)->post(route('administration.imports.customers'), [
            'import_mode' => 'confirm',
            'preview_type' => 'customers',
            'preview_token' => $preview['token'],
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseHas('customers', [
            'short_no' => 2140,
            'sap_no' => '3022664',
        ]);
    }

    public function test_product_matrix_import_supports_preview_before_confirm(): void
    {
        $user = User::factory()->create();

        Container::query()->create([
            'title' => 'alpha-service',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        $csv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Monitoring;Alpha Suite;Kurz;Alias;Beschreibung;Ja;alpha-service/ CustInst.: Nein
CSV;

        $this->actingAs($user)->post(route('product_matrix.import'), [
            'import_mode' => 'preview',
            'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $csv),
        ])->assertRedirect(route('product_matrix.index'));

        $this->assertDatabaseCount('product_matrices', 0);
        $preview = session('product_matrix_import_preview');
        $this->assertIsArray($preview);

        $this->actingAs($user)->post(route('product_matrix.import'), [
            'import_mode' => 'confirm',
            'preview_token' => $preview['token'],
        ])->assertRedirect(route('product_matrix.index'));

        $this->assertDatabaseCount('product_matrices', 1);
        $this->assertDatabaseHas('product_matrices', [
            'product' => 'Alpha Suite',
        ]);
    }

    public function test_compose_import_supports_preview_before_confirm(): void
    {
        $user = User::factory()->create();
        $compose = <<<'YAML'
services:
  alpha:
    image: addons/alpha-service
YAML;

        $this->actingAs($user)->post(route('compose.upload'), [
            'import_mode' => 'preview',
            'compose_files' => [
                UploadedFile::fake()->createWithContent('docker-compose-alpha.yml', $compose),
            ],
        ])->assertRedirect(route('administration.index', ['tab' => 'administration', 'subtab' => 'import']));

        $this->assertDatabaseCount('composers', 0);
        $preview = session('compose_import_preview');
        $this->assertIsArray($preview);

        $this->actingAs($user)->post(route('compose.upload'), [
            'import_mode' => 'confirm',
            'preview_token' => $preview['token'],
        ])->assertRedirect(route('compose.index'));

        $this->assertDatabaseCount('composers', 1);
        $this->assertDatabaseCount('containers', 1);
        $this->assertDatabaseHas('composers', [
            'compose_filename' => 'docker-compose-alpha.yml',
        ]);
    }
}
