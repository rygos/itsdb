<?php

namespace App\Http\Controllers;

use App\Models\Composer;
use App\Models\ComposerContainerRel;
use App\Models\Container;
use App\Models\Server;
use App\Models\ServersComposersRel;
use App\Support\ComposeServiceExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;
use ZipArchive;

class ComposeController extends Controller
{
    private const IMPORT_PREVIEW_SESSION_KEY = 'compose_import_preview';

    public function __construct(private readonly ComposeServiceExporter $composeServiceExporter) {}

    public function generate($server_id)
    {
        $comp = ServersComposersRel::whereServerId($server_id)->get();

        $containers = [];
        $container_dupe_check = [];
        foreach ($comp as $item) {
            foreach ($item->composer->rel()->get() as $c) {
                if (! in_array($c->container->title, $container_dupe_check)) {
                    $containers[] = $c->container;
                    $container_dupe_check[] = $c->container->title;
                }
            }
        }

        $compose = [
            'version' => '3.3',
            'services' => $this->composeServiceExporter->buildServicesPayload($containers),
            'networks' => [
                'u' => [
                    'driver' => 'bridge',
                    'ipam' => [
                        'config' => [
                            0 => [
                                'subnet' => '${USUBNET:-172.18.1.0/24}',
                            ],
                        ],
                    ],
                ],
            ],
            'volumes' => [
                'proxy_vol' => null,
                't1k_vol' => null,
            ],
        ];

        $server = Server::whereId($server_id)->first();
        $server->docker_compose_raw = Yaml::dump($compose, 8, 2);
        $server->save();

        return redirect()->back();
    }

    public function index()
    {
        $comp = Composer::orderBy('title')->get();

        return view('compose.index', [
            'comp' => $comp,
        ]);
    }

    public function show($filename)
    {
        $comp = Composer::whereComposeFilename($filename)->first();

        return view('compose.show', [
            'data' => $comp,
        ]);
    }

    public function store(Request $request, $filename)
    {
        $alt_titles = $request->get('title_alternatives');

        $comp = Composer::whereComposeFilename($filename)->first();
        $comp->title_alternatives = $alt_titles;
        $comp->save();

        return redirect()->route('compose.show', $filename);
    }

    public function upload(Request $request)
    {
        if ($request->input('import_mode') === 'confirm') {
            $preview = $request->session()->get(self::IMPORT_PREVIEW_SESSION_KEY);

            abort_unless(
                is_array($preview) && ($preview['token'] ?? null) === $request->input('preview_token'),
                422,
                'Import-Vorschau nicht mehr gueltig. Bitte Vorschau erneut erzeugen.'
            );

            $request->session()->forget(self::IMPORT_PREVIEW_SESSION_KEY);

            foreach ($preview['uploads'] as $upload) {
                if (! in_array($upload['action'], ['new', 'update'], true)) {
                    continue;
                }

                $this->saveorupdate_compose_from_content(
                    $upload['filename'],
                    Carbon::parse($upload['date']),
                    $upload['content']
                );
            }

            return redirect()->route('compose.index')->with('status', 'Compose-Import ausgefuehrt.');
        }

        $uploads = [];

        $zipFile = $request->file('compose_zip');
        if ($zipFile) {
            $zip = new ZipArchive;
            if ($zip->open($zipFile->getPathname()) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    $name = $stat['name'] ?? '';
                    $filename = basename($name);
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if (! in_array($extension, ['yml', 'yaml'], true)) {
                        continue;
                    }

                    $content = $zip->getFromIndex($i);
                    if ($content === false) {
                        continue;
                    }

                    $mtime = $stat['mtime'] ?? null;
                    $date = $mtime ? Carbon::createFromTimestamp($mtime) : Carbon::now();
                    $uploads[] = [
                        'filename' => $filename,
                        'date' => $date,
                        'content' => $content,
                    ];
                }
                $zip->close();
            }
        }

        $files = $request->file('compose_files', []);
        foreach ((array) $files as $file) {
            if (! $file) {
                continue;
            }

            $filename = $file->getClientOriginalName();
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (! in_array($extension, ['yml', 'yaml'], true)) {
                continue;
            }

            $content = file_get_contents($file->getPathname());
            if ($content === false) {
                continue;
            }

            $mtime = @filemtime($file->getPathname());
            $date = $mtime ? Carbon::createFromTimestamp($mtime) : Carbon::now();
            $uploads[] = [
                'filename' => $filename,
                'date' => $date,
                'content' => $content,
            ];
        }

        $previewUploads = collect($uploads)
            ->map(function (array $upload): array {
                $parsed = Yaml::parse($upload['content']);
                $services = $parsed['services'] ?? [];
                $existing = Composer::query()->where('compose_filename', $upload['filename'])->first();

                if (! is_array($services) || $services === []) {
                    return [
                        'action' => 'conflict',
                        'filename' => $upload['filename'],
                        'title' => 'Keine Services gefunden',
                        'date' => $upload['date']->toIso8601String(),
                        'content' => $upload['content'],
                        'service_count' => 0,
                    ];
                }

                if ($existing && $existing->orig_date && $existing->orig_date->gt($upload['date'])) {
                    return [
                        'action' => 'skip',
                        'filename' => $upload['filename'],
                        'title' => 'Bestehende Datei ist neuer',
                        'date' => $upload['date']->toIso8601String(),
                        'content' => $upload['content'],
                        'service_count' => count($services),
                    ];
                }

                return [
                    'action' => $existing ? 'update' : 'new',
                    'filename' => $upload['filename'],
                    'title' => $existing ? 'Compose-Datei wird aktualisiert' : 'Neue Compose-Datei',
                    'date' => $upload['date']->toIso8601String(),
                    'content' => $upload['content'],
                    'service_count' => count($services),
                ];
            })
            ->all();

        if ($request->input('import_mode') !== 'preview') {
            foreach ($previewUploads as $upload) {
                if (! in_array($upload['action'], ['new', 'update'], true)) {
                    continue;
                }

                $this->saveorupdate_compose_from_content(
                    $upload['filename'],
                    Carbon::parse($upload['date']),
                    $upload['content']
                );
            }

            return redirect()->route('compose.index');
        }

        $token = (string) str()->uuid();
        $request->session()->put(self::IMPORT_PREVIEW_SESSION_KEY, [
            'token' => $token,
            'uploads' => $previewUploads,
            'summary' => collect($previewUploads)->countBy('action')->all(),
        ]);

        return redirect()->route('administration.index', ['tab' => 'administration', 'subtab' => 'import'])
            ->with('status', 'Vorschau fuer Compose-Import erstellt.');
    }

    private function saveorupdate_compose_from_content($filename, Carbon $date, $content)
    {
        $title = str_replace('docker-compose-', '', explode('.', $filename)[0]);
        $file_url = 'upload';
        $parsed = Yaml::parse($content);
        $services = $parsed['services'] ?? [];
        if (! $services) {
            return;
        }

        $check = Composer::whereComposeFilename($filename)->first();
        if ($check) { // Vorhanden
            if ($check->orig_date <= $date) { // Älter also Update
                $check->orig_date = $date;
                $check->orig_compose = $content;
                $check->title = $title;
                $check->orig_url = $file_url;
                $check->compose_filename = $filename;
                $check->save();

                foreach ($services as $item) {
                    $this->saveorupdate_container($item, $date, $check->id);
                }

            }
        } else { // Nicht vorhanden also anlegen
            $c = new Composer;
            $c->orig_date = $date;
            $c->orig_compose = $content;
            $c->title = $title;
            $c->orig_url = $file_url;
            $c->compose_filename = $filename;
            $c->save();

            foreach ($services as $item) {
                $this->saveorupdate_container($item, $date, $c->id);
            }

        }
    }

    public function saveorupdate_container($data, $date, $compose_id)
    {
        $title = explode('/', $data['image'])[1];

        $check = Container::whereTitle($title)->first();
        if ($check) { // Gefunden, Update
            if ($check->content_orig_date <= Carbon::parse($date)) { // Neuere Daten vorhanden.
                $check->title = $title;
                $check->content_orig_date = Carbon::parse($date);
                $check->content_orig = Yaml::dump($data);
                $check->save();
                $this->add_rel($compose_id, $check->id);
            } else {
                $this->add_rel($compose_id, $check->id);
            }
        } else { // Nicht gefunden, anlegen
            $c = new Container;
            $c->content_orig = Yaml::dump($data);
            $c->title = $title;
            $c->content_orig_date = Carbon::parse($date);
            $c->save();
            $this->add_rel($compose_id, $c->id);
        }

    }

    public function add_rel($compose_id, $container_id)
    {
        $check = ComposerContainerRel::whereContainerId($container_id)->where('composer_id', $compose_id)->first();
        if (! $check) {
            $rel = new ComposerContainerRel;
            $rel->container_id = $container_id;
            $rel->composer_id = $compose_id;
            $rel->save();
        }
    }
}
