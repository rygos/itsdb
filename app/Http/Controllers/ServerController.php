<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Composer;
use App\Models\Container;
use App\Models\Env;
use App\Models\OperatingSystem;
use App\Models\ProductMatrix;
use App\Models\Server;
use App\Models\ServerKind;
use App\Models\ServersComposersRel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Yaml\Yaml;

class ServerController extends Controller
{
    private function serverKindOptions(): array
    {
        return ['' => ''] + ServerKind::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function operatingSystemOptions(): array
    {
        return ['' => ''] + OperatingSystem::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function buildComposeWorkspaceData(Server $server, iterable $composeRelations): array
    {
        $containers = Container::query()
            ->with('productMatrices')
            ->orderBy('title')
            ->get();

        $attachedCompose = collect($composeRelations)->map(function (ServersComposersRel $relation): array {
            $composer = $relation->composer;

            return [
                'id' => (string) $relation->composer_id,
                'title' => $composer?->title ?? 'Ohne Titel',
                'filename' => $composer?->compose_filename ?? '',
                'container_titles' => $composer?->rel->pluck('container.title')->filter()->values()->all() ?? [],
            ];
        })->values()->all();

        $containerWorkspace = $containers->map(function (Container $container): array {
            $content = trim((string) ($container->content ?: $container->content_orig ?: ''));
            $productMatrices = $container->productMatrices
                ->sortBy(fn (ProductMatrix $productMatrix) => mb_strtolower($productMatrix->product))
                ->values();

            return [
                'id' => (string) $container->id,
                'title' => $container->title,
                'search' => mb_strtolower($container->title.' '.$productMatrices->pluck('product')->implode(' ')),
                'snippet' => $this->buildComposeServiceSnippet($container->title, $content),
                'product_ids' => $productMatrices->pluck('id')->map(fn ($id) => (string) $id)->all(),
                'product_labels' => $productMatrices->map(fn (ProductMatrix $productMatrix) => $productMatrix->product)->all(),
            ];
        })->values();

        $productWorkspace = $containers
            ->flatMap(function (Container $container) {
                return $container->productMatrices->map(function (ProductMatrix $productMatrix) use ($container): array {
                    return [
                        'id' => (string) $productMatrix->id,
                        'label' => $productMatrix->product,
                        'category' => $productMatrix->category,
                        'function' => $productMatrix->function_name,
                        'short_description' => $productMatrix->short_description,
                        'synonyms' => $productMatrix->synonyms,
                        'container_id' => (string) $container->id,
                        'container_title' => $container->title,
                    ];
                });
            })
            ->groupBy('id')
            ->map(function ($rows) {
                $first = $rows->first();
                $containerIds = $rows->pluck('container_id')->unique()->sort()->values()->all();
                $containerTitles = $rows->pluck('container_title')->unique()->sort()->values()->all();

                return [
                    'id' => $first['id'],
                    'label' => $first['label'],
                    'category' => $first['category'],
                    'function' => $first['function'],
                    'search' => mb_strtolower(implode(' ', [
                        $first['label'],
                        $first['category'],
                        $first['function'],
                        $first['short_description'] ?? '',
                        $first['synonyms'] ?? '',
                        implode(' ', $containerTitles),
                    ])),
                    'container_ids' => $containerIds,
                    'container_titles' => $containerTitles,
                ];
            })
            ->sortBy([
                ['category', 'asc'],
                ['function', 'asc'],
                ['label', 'asc'],
            ])
            ->values()
            ->all();

        return [
            'saved_compose_raw' => (string) ($server->docker_compose_raw ?? ''),
            'baseline_service_titles' => $this->extractComposeServiceTitles($server->docker_compose_raw),
            'containers' => $containerWorkspace->all(),
            'products' => $productWorkspace,
            'attached_compose' => $attachedCompose,
        ];
    }

    private function buildComposeServiceSnippet(string $title, string $content): string
    {
        if ($content === '') {
            return '';
        }

        $indentedContent = collect(preg_split("/\r\n|\n|\r/", $content) ?: [])
            ->map(fn (string $line): string => $line === '' ? $line : '    '.$line)
            ->implode("\n");

        return "  {$title}:\n{$indentedContent}";
    }

    private function extractComposeServiceTitles(?string $composeRaw): array
    {
        if (! is_string($composeRaw) || trim($composeRaw) === '') {
            return [];
        }

        try {
            $parsed = Yaml::parse($composeRaw);
            $services = is_array($parsed) ? ($parsed['services'] ?? null) : null;
            if (is_array($services)) {
                return array_values(array_map('strval', array_keys($services)));
            }
        } catch (\Throwable) {
        }

        $services = [];
        $lines = preg_split("/\r\n|\n|\r/", $composeRaw) ?: [];
        $servicesIndent = null;
        $inServices = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            preg_match('/^(\s*)/', $line, $matches);
            $indent = isset($matches[1]) ? strlen($matches[1]) : 0;

            if (! $inServices && $trimmed === 'services:') {
                $inServices = true;
                $servicesIndent = $indent;

                continue;
            }

            if (! $inServices) {
                continue;
            }

            if ($indent <= $servicesIndent) {
                break;
            }

            if ($indent === $servicesIndent + 2 && preg_match('/^["\']?([A-Za-z0-9._-]+)["\']?:\s*$/', $trimmed, $serviceMatches) === 1) {
                $services[] = $serviceMatches[1];
            }
        }

        return array_values(array_unique($services));
    }

    public function view($id): View
    {
        $s = Server::with(['customer.servers.serverKind', 'customer.servers.operatingSystem', 'credentials.servers', 'serverKind', 'operatingSystem'])->whereId($id)->firstOrFail();
        $certs['server'] = openssl_x509_parse($s->server_cert_raw);
        $certs['intermediate'] = openssl_x509_parse($s->customer->intermediate_cert_raw);
        $certs['root'] = openssl_x509_parse($s->customer->root_cert_raw);
        if ($s->private_key_raw) {
            if (openssl_pkey_get_private($s->private_key_raw)) {
                $certs['key'] = openssl_pkey_get_details(openssl_pkey_get_private($s->private_key_raw));
            } else {
                $certs['key'] = false;
            }
        } else {
            $certs['key'] = false;
        }

        $compose = ServersComposersRel::query()
            ->with(['composer.rel.container'])
            ->whereServerId($id)
            ->get();

        $added_compose = [];
        foreach ($compose as $item) {
            $added_compose[] = $item->composer_id;
        }

        $comp_data = Composer::query()
            ->whereNotIn('id', $added_compose)
            ->orderBy('title')
            ->get();
        $comp_select = [];
        foreach ($comp_data as $item) {
            if (! is_null($item->title_alternatives)) {
                $comp_select[$item->id] = $item->title.' ('.$item->title_alternatives.')';
            } else {
                $comp_select[$item->id] = $item->title;
            }
        }

        $env = Env::whereServerId($id)->get();

        return view('servers.view', [
            'server' => $s,
            'certs' => $certs,
            'compose_select' => $comp_select,
            'compose' => $compose,
            'composeWorkspaceData' => $this->buildComposeWorkspaceData($s, $compose),
            'env' => $env,
            'credentials' => $s->credentials()->with('servers')->orderBy('type')->orderBy('username')->get(),
            'serverKindOptions' => $this->serverKindOptions(),
            'operatingSystemOptions' => $this->operatingSystemOptions(),
            // 'env_needed' => $env_needed,
        ]);
    }

    public function add_composer(Request $request, $id): RedirectResponse
    {
        // $id = Server_ID
        $sc = new ServersComposersRel;
        $sc->composer_id = $request->get('compose');
        $sc->server_id = $id;
        $sc->save();

        return redirect()->back();
    }

    public function del_composer($server_id, $compose_id): RedirectResponse
    {
        $sc = ServersComposersRel::whereServerId($server_id)->where('composer_id', $compose_id)->first();
        $sc->delete();

        return redirect()->back();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_kind_id' => ['nullable', 'integer', 'exists:server_kinds,id'],
            'operating_system_id' => ['nullable', 'integer', 'exists:operating_systems,id'],
        ]);

        $s = new Server;
        $s->type = $request->get('type');
        $s->server_kind_id = $validated['server_kind_id'] ?? null;
        $s->operating_system_id = $validated['operating_system_id'] ?? null;
        $s->servername = $request->get('servername');
        $s->fqdn = $request->get('fqdn');
        $s->db_sid = $request->get('db_sid');
        $s->db_server = $request->get('db_server');
        $s->ext_ip = $request->get('ext_ip');
        $s->int_ip = $request->get('int_ip');
        $s->user_id = auth()->id();
        $s->customer_id = $request->get('customer_id');
        $s->save();

        LogHelper::log('customer', $s->customer_id, 'Server', 'Add '.$s->type.' Server: '.$s->servername);
        LogHelper::log('server', $s->id, 'Server', 'Add '.$s->type.' Server: '.$s->servername);

        return \Redirect::back();
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_kind_id' => ['nullable', 'integer', 'exists:server_kinds,id'],
            'operating_system_id' => ['nullable', 'integer', 'exists:operating_systems,id'],
        ]);

        $s = Server::whereId($request->get('server_id'))->first();
        $s->type = $request->get('type');
        $s->server_kind_id = $validated['server_kind_id'] ?? null;
        $s->operating_system_id = $validated['operating_system_id'] ?? null;
        $s->servername = $request->get('servername');
        $s->fqdn = $request->get('fqdn');
        $s->db_sid = $request->get('db_sid');
        $s->db_server = $request->get('db_server');
        $s->ext_ip = $request->get('ext_ip');
        $s->int_ip = $request->get('int_ip');
        $s->save();

        LogHelper::log('server', $s->id, 'Update', 'Update Server Informations: '.$s->type.', '.$s->servername.', '.$s->fqdn.', '.$s->db_sid.', '.$s->db_server);

        return \Redirect::back();
    }

    public function update_serverconfig(Request $request): RedirectResponse
    {
        $s = Server::whereId($request->get('server_id'))->first();
        $s->env_raw = $request->get('env');
        $s->docker_compose_raw = $request->get('docker_compose');
        $s->save();

        LogHelper::log('server', $s->id, 'ENV/Docker', 'Update Docker/ENV custom configuration');

        return redirect()->back();
    }
}
