<?php

namespace App\Http\Controllers;

use App\Models\Composer;
use App\Models\Customer;
use App\Models\Env;
use App\Models\Server;
use App\Models\ServersComposersRel;
use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

class ServerController extends Controller
{
    public function view($id){
        $s = Server::whereId($id)->first();
        $certs['server'] = openssl_x509_parse($s->server_cert_raw);
        $certs['intermediate'] = openssl_x509_parse($s->customer->intermediate_cert_raw);
        $certs['root'] = openssl_x509_parse($s->customer->root_cert_raw);
        if($s->private_key_raw){
            if(openssl_pkey_get_private($s->private_key_raw)){
                $certs['key'] = openssl_pkey_get_details(openssl_pkey_get_private($s->private_key_raw));
            }else{
                $certs['key'] = false;
            }
        }else{
            $certs['key'] = false;
        }

        $compose = ServersComposersRel::whereServerId($id)->get();

        $added_compose = [];
        foreach ($compose as $item){
            $added_compose[] = $item->composer_id;
        }

        $comp_data = Composer::whereNotIn('id', $added_compose)->get();
        $comp_select = [];
        foreach ($comp_data as $item){
            if(!is_null($item->title_alternatives)){
                $comp_select[$item->id] = $item->title.' ('.$item->title_alternatives.')';
            }else{
                $comp_select[$item->id] = $item->title;
            }
        }

        $env = Env::whereServerId($id)->get();

        return view('servers.view', [
            'server' => $s,
            'certs' => $certs,
            'compose_select' => $comp_select,
            'compose' => $compose,
            'env' => $env,
            //'env_needed' => $env_needed,
        ]);
    }

    public function add_composer(Request $request, $id){
        //$id = Server_ID
        $sc = new ServersComposersRel;
        $sc->composer_id = $request->get('compose');
        $sc->server_id = $id;
        $sc->save();

        return redirect()->back();
    }

    public function del_composer($server_id, $compose_id){
        $sc = ServersComposersRel::whereServerId($server_id)->where('composer_id', $compose_id)->first();
        $sc->delete();

        return redirect()->back();
    }

    public function store(Request $request){
        $s = new Server;
        $s->type = $request->get('type');
        $s->servername = $request->get('servername');
        $s->fqdn = $request->get('fqdn');
        $s->db_sid = $request->get('db_sid');
        $s->db_server = $request->get('db_server');
        $s->ext_ip = $request->get('ext_ip');
        $s->int_ip = $request->get('int_ip');
        $s->user_id = 1;
        $s->customer_id = $request->get('customer_id');
        $s->save();

        return \Redirect::back();
    }

    public function update(Request $request){
        $s = Server::whereId($request->get('server_id'))->first();
        $s->type = $request->get('type');
        $s->servername = $request->get('servername');
        $s->fqdn = $request->get('fqdn');
        $s->db_sid = $request->get('db_sid');
        $s->db_server = $request->get('db_server');
        $s->ext_ip = $request->get('ext_ip');
        $s->int_ip = $request->get('int_ip');
        $s->save();

        return \Redirect::back();
    }

    public function update_serverconfig(Request $request){
        $s = Server::whereId($request->get('server_id'))->first();
        $s->env_raw = $request->get('env');
        $s->docker_compose_raw = $request->get('docker_compose');
        $s->save();

        return redirect()->back();
    }
}
