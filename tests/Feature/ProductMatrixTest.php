<?php

namespace Tests\Feature;

use App\Models\Container;
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

        ProductMatrix::create([
            'position' => 1,
            'category' => 'ORBIS',
            'function_name' => 'Monitoring',
            'product' => 'Alpha Suite',
        ]);

        ProductMatrix::create([
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
}
