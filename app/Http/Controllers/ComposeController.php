<?php

namespace App\Http\Controllers;

use App\Models\Composer;
use App\Models\ComposerContainerRel;
use App\Models\Container;
use App\Models\Server;
use App\Models\ServersComposersRel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

class ComposeController extends Controller
{
    public function generate($server_id){
        $comp = ServersComposersRel::whereServerId($server_id)->get();

        //$test = Composer::whereId(1)->first()->orig_compose;
        //dd(Yaml::parse($test));

        $container = [];
        $container_dupe_check = [];
        foreach ($comp as $item){
            foreach($item->composer->rel()->get() as $c){
                if(!in_array($c->container->title, $container_dupe_check)){
                    if($c->container->content == ''){
                        $container[$c->container->title] = Yaml::parse($c->container->content_orig);
                        $container_dupe_check[] = $c->container->title;
                    }else{
                        $container[$c->container->title] = Yaml::parse($c->container->content);
                        $container_dupe_check[] = $c->container->title;
                    }
                }
            }
        }

        $compose = [
            'version' => '3.3',
            'services' => $container,
            'networks' => [
                'u' => [
                    'driver' => 'bridge',
                    'ipam' => [
                        'config' => [
                            0 => [
                                'subnet' => '${USUBNET:-172.18.1.0/24}'
                            ]
                        ]
                    ]
                ]
            ],
            'volumes' => [
                'proxy_vol' => null,
                't1k_vol' => null
            ]
        ];

        $server = Server::whereId($server_id)->first();
        $server->docker_compose_raw = Yaml::dump($compose, 8,2);
        $server->save();

        return redirect()->back();
    }

    public function index(){
        $comp = Composer::orderBy('title')->get();

        return view('compose.index', [
            'comp' => $comp
        ]);
    }

    public function show($filename){
        $comp = Composer::whereComposeFilename($filename)->first();

        return view('compose.show', [
            'data' => $comp
        ]);
    }

    public function store(Request $request, $filename){
        $alt_titles = $request->get('title_alternatives');

        $comp = Composer::whereComposeFilename($filename)->first();
        $comp->title_alternatives = $alt_titles;
        $comp->save();

        return redirect()->route('compose.show', $filename);
    }

    public function update(){
        $repo_url = env('REPO_URL');
        $html = file_get_contents($repo_url);
        $rows = preg_match_all('/<a href="([^"]+)">[^<]*<\/a>[\s*]+([^"]+)...:/i', $html, $files);

        for($i = 0; $i < $rows; ++$i){
            if(str_ends_with($files[1][$i], ".yml")
                and $files[1][$i] != 'docker-compose-strimzi.yml'
                and $files[1][$i] != 'docker-compose-strimzi-crd.yml'
                and $files[1][$i] != 'docker-compose-orbis-events-4u.yml'
                and $files[1][$i] != 'docker-compose-oas-collector.yml'
                and $files[1][$i] != 'docker-compose-ha-service.yml'
                and $files[1][$i] != 'docker-compose-kafka.yml'
            ){
                $this->saveorupdate_compose($files[1][$i], $files[2][$i]);
            }
        }

        return redirect()->route('compose.index');
    }

    function saveorupdate_compose($filename, $date){
        $file_url = env('REPO_URL').$filename;
        $title = str_replace('docker-compose-','',explode('.', $filename)[0]);

        $check = Composer::whereComposeFilename($filename)->first();
        if($check){ //Vorhanden
            if($check->orig_date <= Carbon::parse($date)){ //Älter also Update
                $check->orig_date = Carbon::parse($date);
                $check->orig_compose = file_get_contents($file_url);
                $check->title = $title;
                $check->orig_url = $file_url;
                $check->compose_filename = $filename;
                $data = Yaml::parse($check->orig_compose)['services'];
                $check->save();

                foreach ($data as $item){
                    $this->saveorupdate_container($item, $date, $check->id);
                }

            }
        }else{ //Nicht vorhanden also anlegen
            $c = new Composer;
            $c->orig_date = Carbon::parse($date);
            $c->orig_compose = file_get_contents($file_url);
            $c->title = $title;
            $c->orig_url = $file_url;
            $c->compose_filename = $filename;
            $data = Yaml::parse($c->orig_compose)['services'];
            $c->save();

            foreach ($data as $item){
                $this->saveorupdate_container($item, $date, $c->id);
            }

        }
    }

    function saveorupdate_container($data, $date, $compose_id){
        $title = explode('/', $data['image'])[1];

        $check = Container::whereTitle($title)->first();
        if($check){ //Gefunden, Update
            if($check->content_orig_date <= Carbon::parse($date)){ //Neuere Daten vorhanden.
                $check->title = $title;
                $check->content_orig_date = Carbon::parse($date);
                $check->content_orig = Yaml::dump($data);
                $check->save();
                $this->add_rel($compose_id, $check->id);
            }else{
                $this->add_rel($compose_id, $check->id);
            }
        }else{ //Nicht gefunden, anlegen
            $c = new Container;
            $c->content_orig = Yaml::dump($data);
            $c->title = $title;
            $c->content_orig_date = Carbon::parse($date);
            $c->save();
            $this->add_rel($compose_id, $c->id);
        }

    }

    function add_rel($compose_id, $container_id){
        $check = ComposerContainerRel::whereContainerId($container_id)->where('composer_id', $compose_id)->first();
        if(!$check){
            $rel = new ComposerContainerRel;
            $rel->container_id = $container_id;
            $rel->composer_id = $compose_id;
            $rel->save();
        }
    }
}
