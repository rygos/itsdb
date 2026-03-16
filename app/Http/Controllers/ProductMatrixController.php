<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerImportAlias;
use App\Models\ProductMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductMatrixController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $query = ProductMatrix::with('containers')->orderBy('position');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('product', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('function_name', 'like', '%' . $search . '%')
                    ->orWhere('synonyms', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return view('product_matrix.index', [
            'entries' => $query->get(),
            'search' => $search,
            'aliases' => ContainerImportAlias::with('container')->orderBy('source_name')->get(),
            'containers' => Container::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file'],
        ]);

        $uploadedFile = $request->file('csv_file');
        $originalExtension = strtolower((string) $uploadedFile->getClientOriginalExtension());

        if (!in_array($originalExtension, ['csv', 'txt'], true)) {
            return redirect()
                ->route('product_matrix.index')
                ->withInput()
                ->withErrors([
                    'csv_file' => 'Bitte eine CSV-Datei mit der Endung .csv oder .txt hochladen.',
                ]);
        }

        [$records, $missingContainers] = $this->parseCsvImport($uploadedFile->getRealPath());

        if (!empty($missingContainers)) {
            $missingList = implode(', ', array_slice($missingContainers, 0, 12));
            $suffix = count($missingContainers) > 12 ? ' ...' : '';

            return redirect()
                ->route('product_matrix.index')
                ->withInput()
                ->withErrors([
                    'csv_file' => 'Import abgebrochen. Fehlende Container in der Datenbank: ' . $missingList . $suffix,
                ]);
        }

        DB::transaction(function () use ($records) {
            ProductMatrix::query()->delete();

            foreach ($records as $record) {
                $entry = ProductMatrix::create([
                    'position' => $record['position'],
                    'category' => $record['category'],
                    'function_name' => $record['function_name'],
                    'product' => $record['product'],
                    'short_description' => $record['short_description'],
                    'synonyms' => $record['synonyms'],
                    'description' => $record['description'],
                ]);

                $entry->containers()->sync($record['container_ids']);
            }
        });

        return redirect()
            ->route('product_matrix.index')
            ->with('status', count($records) . ' Produkte importiert.');
    }

    public function storeAlias(Request $request)
    {
        $data = $this->validateAlias($request);

        ContainerImportAlias::create($data);

        return redirect()
            ->route('product_matrix.index')
            ->with('status', 'Alias gespeichert.');
    }

    public function updateAlias(Request $request, $id)
    {
        $alias = ContainerImportAlias::findOrFail($id);
        $data = $this->validateAlias($request, $alias->id);

        $alias->update($data);

        return redirect()
            ->route('product_matrix.index')
            ->with('status', 'Alias aktualisiert.');
    }

    public function deleteAlias($id)
    {
        $alias = ContainerImportAlias::findOrFail($id);
        $alias->delete();

        return redirect()
            ->route('product_matrix.index')
            ->with('status', 'Alias gelöscht.');
    }

    private function parseCsvImport(string $path): array
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            return [[], ['CSV-Datei konnte nicht gelesen werden']];
        }

        $contents = $this->normalizeEncoding($contents);

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contents);
        rewind($stream);

        $headers = fgetcsv($stream, 0, ';');
        if ($headers === false) {
            fclose($stream);

            return [[], ['CSV-Datei ist leer']];
        }

        $headers = array_map(function ($header) {
            return trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $header));
        }, $headers);

        $containersByTitle = [];
        foreach (Container::query()->get(['id', 'title']) as $container) {
            $containersByTitle[$this->normalizeContainerTitle($container->title)] = $container;
        }

        $aliasesBySource = [];
        foreach (ContainerImportAlias::with('container')->get() as $alias) {
            $aliasesBySource[$this->normalizeContainerTitle($alias->source_name)] = $alias;
        }

        $records = [];
        $missingContainers = [];
        $position = 0;

        while (($row = fgetcsv($stream, 0, ';')) !== false) {
            if ($row === [null] || count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $assoc = [];
            foreach ($headers as $index => $header) {
                $assoc[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            }

            $record = [
                'position' => ++$position,
                'category' => $assoc['Kategorie'] ?? '',
                'function_name' => $assoc['Funktion'] ?? '',
                'product' => $assoc['Produkt'] ?? '',
                'short_description' => $assoc['Kurzbeschreibung'] ?? '',
                'synonyms' => $assoc['Synonyme'] ?? '',
                'description' => $assoc['Beschreibung'] ?? '',
                'container_ids' => [],
            ];

            $containerIds = [];
            foreach ($this->extractContainerTitles($assoc['ORBIS U Spezifkation'] ?? '') as $title) {
                $normalizedTitle = $this->normalizeContainerTitle($title);
                $alias = $aliasesBySource[$normalizedTitle] ?? null;

                if ($alias) {
                    if ($alias->ignore_on_import) {
                        continue;
                    }

                    if ($alias->container) {
                        $containerIds[] = $alias->container->id;
                        continue;
                    }
                }

                $container = $containersByTitle[$normalizedTitle] ?? null;

                if (!$container) {
                    $missingContainers[$normalizedTitle] = $title;
                    continue;
                }

                $containerIds[] = $container->id;
            }

            $record['container_ids'] = array_values(array_unique($containerIds));
            $records[] = $record;
        }

        fclose($stream);

        return [$records, array_values($missingContainers)];
    }

    private function extractContainerTitles(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $parts = preg_split('/<br\s*\/?>|\R/i', $value) ?: [];
        $titles = [];

        foreach ($parts as $part) {
            $part = html_entity_decode(strip_tags(trim($part)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $part = preg_replace('/\s+/', ' ', $part ?? '');
            $part = trim((string) $part);
            if ($part === '') {
                continue;
            }

            $title = trim(explode('/', $part)[0]);
            if ($title === '') {
                continue;
            }

            $titles[$this->normalizeContainerTitle($title)] = $title;
        }

        return array_values($titles);
    }

    private function normalizeContainerTitle(string $title): string
    {
        $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = str_replace("\xc2\xa0", ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title ?? '');

        return mb_strtolower(trim((string) $title));
    }

    private function normalizeEncoding(string $contents): string
    {
        $encoding = mb_detect_encoding($contents, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);

        if ($encoding === false || $encoding === 'UTF-8') {
            return $contents;
        }

        return mb_convert_encoding($contents, 'UTF-8', $encoding);
    }

    private function validateAlias(Request $request, ?int $aliasId = null): array
    {
        $validated = $request->validate([
            'source_name' => ['required', 'string'],
            'container_id' => ['nullable', 'integer', 'exists:containers,id'],
            'ignore_on_import' => ['nullable', 'boolean'],
        ]);

        $sourceName = trim((string) ($validated['source_name'] ?? ''));
        $ignore = (bool) ($validated['ignore_on_import'] ?? false);
        $containerId = $validated['container_id'] ?? null;
        $normalizedSourceName = $this->normalizeContainerTitle($sourceName);

        if ($normalizedSourceName === '') {
            throw ValidationException::withMessages([
                'source_name' => 'Aliasname darf nicht leer sein.',
            ]);
        }

        $duplicateQuery = ContainerImportAlias::query()
            ->get()
            ->first(function (ContainerImportAlias $alias) use ($normalizedSourceName, $aliasId) {
                if ($aliasId !== null && $alias->id === $aliasId) {
                    return false;
                }

                return $this->normalizeContainerTitle($alias->source_name) === $normalizedSourceName;
            });

        if ($duplicateQuery) {
            throw ValidationException::withMessages([
                'source_name' => 'Für diesen Alias existiert bereits ein Eintrag.',
            ]);
        }

        if (!$ignore && !$containerId) {
            throw ValidationException::withMessages([
                'container_id' => 'Bitte Ziel-Container auswählen oder Import ignorieren aktivieren.',
            ]);
        }

        return [
            'source_name' => $sourceName,
            'container_id' => $ignore ? null : $containerId,
            'ignore_on_import' => $ignore,
        ];
    }
}
