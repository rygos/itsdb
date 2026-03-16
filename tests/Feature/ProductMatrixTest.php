<?php

namespace Tests\Feature;

use App\Models\Container;
use App\Models\ContainerImportAlias;
use App\Models\ProductMatrix;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductMatrixTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function test_product_matrix_import_creates_entries_and_container_links()
    {
        $user = User::factory()->create();

        $alpha = Container::create([
            'title' => 'alpha-service',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        $beta = Container::create([
            'title' => 'beta-service',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        $csv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Monitoring;Alpha Suite;Kurz;Alias;"Beschreibung mit
Zeilenumbruch";Ja;"alpha-service/ CustInst.: Nein<br>beta-service/ CustInst.: Nein"
CSV;

        $response = $this->actingAs($user)->post(route('product_matrix.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $csv),
        ]);

        $response->assertRedirect(route('product_matrix.index'));
        $this->assertDatabaseCount('product_matrices', 1);
        $this->assertDatabaseCount('container_product_matrix', 2);

        $entry = ProductMatrix::with('containers')->first();
        $this->assertSame('Alpha Suite', $entry->product);
        $this->assertSame([$alpha->id, $beta->id], $entry->containers->pluck('id')->sort()->values()->all());
    }

    public function test_product_matrix_import_fails_when_container_is_missing()
    {
        $user = User::factory()->create();

        $csv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Monitoring;Alpha Suite;Kurz;Alias;Beschreibung;Ja;missing-service/ CustInst.: Nein
CSV;

        $response = $this->from(route('product_matrix.index'))
            ->actingAs($user)
            ->post(route('product_matrix.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $csv),
            ]);

        $response->assertRedirect(route('product_matrix.index'));
        $response->assertSessionHasErrors('csv_file');
        $this->assertDatabaseCount('product_matrices', 0);
    }

    public function test_product_matrix_search_filters_products()
    {
        $user = User::factory()->create();

        $alpha = Container::create([
            'title' => 'alpha-service',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        $entry = ProductMatrix::create([
            'import_key' => sha1('alpha'),
            'position' => 1,
            'category' => 'ORBIS',
            'function_name' => 'Monitoring',
            'product' => 'Alpha Suite',
        ]);
        $entry->containers()->sync([$alpha->id]);

        ProductMatrix::create([
            'import_key' => sha1('beta'),
            'position' => 2,
            'category' => 'ORBIS',
            'function_name' => 'Archiv',
            'product' => 'Beta Viewer',
        ]);

        $response = $this->actingAs($user)->get(route('product_matrix.index', ['search' => 'Alpha']));

        $response->assertOk();
        $response->assertSee('Alpha Suite');
        $response->assertDontSee('Beta Viewer');
    }

    public function test_product_matrix_import_uses_alias_mapping_for_containers()
    {
        $user = User::factory()->create();

        $target = Container::create([
            'title' => 'orbis-user-provisioning',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        ContainerImportAlias::create([
            'source_name' => 'user-provisioning',
            'container_id' => $target->id,
            'ignore_on_import' => false,
        ]);

        $csv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Provisioning;Alias Suite;Kurz;Alias;Beschreibung;Ja;user-provisioning/ CustInst.: Nein
CSV;

        $response = $this->actingAs($user)->post(route('product_matrix.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $csv),
        ]);

        $response->assertRedirect(route('product_matrix.index'));

        $entry = ProductMatrix::with('containers')->first();
        $this->assertSame([$target->id], $entry->containers->pluck('id')->all());
    }

    public function test_product_matrix_import_can_ignore_container_aliases()
    {
        $user = User::factory()->create();

        ContainerImportAlias::create([
            'source_name' => 'legacy-service',
            'container_id' => null,
            'ignore_on_import' => true,
        ]);

        $csv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Provisioning;Ignore Suite;Kurz;Alias;Beschreibung;Ja;legacy-service/ CustInst.: Nein
CSV;

        $response = $this->actingAs($user)->post(route('product_matrix.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $csv),
        ]);

        $response->assertRedirect(route('product_matrix.index'));
        $response->assertSessionHas('status');
        $this->assertDatabaseCount('container_product_matrix', 0);
        $this->assertDatabaseCount('product_matrices', 0);
    }

    public function test_product_matrix_reimport_updates_existing_entries_without_duplicates()
    {
        $user = User::factory()->create();

        $alpha = Container::create([
            'title' => 'alpha-service',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        $beta = Container::create([
            'title' => 'beta-service',
            'content' => null,
            'content_orig' => 'services: {}',
            'content_orig_date' => now(),
        ]);

        $firstCsv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Monitoring;Alpha Suite;Kurz;Alias;Erste Beschreibung;Ja;alpha-service/ CustInst.: Nein
CSV;

        $secondCsv = <<<'CSV'
Kategorie;Funktion;Produkt;Kurzbeschreibung;Synonyme;Beschreibung;ORBIS U Technologie?;ORBIS U Spezifkation
ORBIS;Monitoring;Alpha Suite;Kurz;Alias;Aktualisierte Beschreibung;Ja;"alpha-service/ CustInst.: Nein<br>beta-service/ CustInst.: Nein"
CSV;

        $this->actingAs($user)->post(route('product_matrix.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $firstCsv),
        ])->assertRedirect(route('product_matrix.index'));

        $this->actingAs($user)->post(route('product_matrix.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('produkte.csv', $secondCsv),
        ])->assertRedirect(route('product_matrix.index'));

        $this->assertDatabaseCount('product_matrices', 1);
        $this->assertDatabaseHas('product_matrices', [
            'product' => 'Alpha Suite',
            'description' => 'Aktualisierte Beschreibung',
        ]);
        $this->assertDatabaseCount('container_product_matrix', 2);
    }
}
