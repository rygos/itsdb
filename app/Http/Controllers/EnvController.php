<?php

namespace App\Http\Controllers;

use App\Models\Env;
use App\Models\Server;
use Dotenv\Dotenv;
use Illuminate\Http\Request;

class EnvController extends Controller
{
    public function update(Request $request, $server_id){
        $env = Env::whereServerId($server_id)->get();
        foreach ($env as $item){
            if(!is_null($request->get($item->key))){
                $item->value = $request->get($item->key);
                $item->save();
            }
        }

        return redirect()->back();
    }

    public function generate($server_id){
        $compose = Server::whereId($server_id)->first()->docker_compose_raw;

        preg_match_all('/\${(.*?)}/', $compose, $r_yml);
        $env_needed = [];
        foreach ($r_yml[1] as $i){
            if(str_contains($i, ':-')){
                $tmp['key'] = explode(':-', $i)[0];
                $tmp['needed'] = 0;
                $tmp['value'] = explode(':-', $i)[1];
                $env_needed[] = $tmp;
            }else{
                $tmp['key'] = $i;
                $tmp['needed'] = 1;
                $tmp['value'] = '';
                $env_needed[] = $tmp;
            }
        }

        foreach ($env_needed as $item){
            $check = Env::whereServerId($server_id)->where('key', $item['key'])->first();
            if(!$check){
                $env = new Env;
                $env->server_id = $server_id;
                $env->key = $item['key'];
                $env->value = $item['value'];
                $env->needed = $item['needed'];
                $env->save();
            }
        }

        return redirect()->back();
    }

    public function generate_from_raw($server_id){
        $server = Server::whereId($server_id)->first();

        $raw = $server->env_raw;
        $raw_array = preg_split("/\r\n|\n|\r/", $raw);

        foreach ($raw_array as $item){
            $tmp['key'] = explode('=', $item)[0];
            $tmp['value'] = explode('=', $item)[1];
            $tmp['needed'] = 0;

            $check = Env::whereServerId($server_id)->where('key', $tmp['key'])->first();
            if(!$check){
                $env = new Env;
                $env->server_id = $server_id;
                $env->key = $tmp['key'];
                $env->value = $tmp['value'];
                $env->needed = $tmp['needed'];
                $env->save();
            }else{
                $check->value = $tmp['value'];
                $check->save;
            }
        }

        return redirect()->back();
    }

    public function generate_raw($server_id){
        $data = Env::whereServerId($server_id)->orderBy('key')->get();

        $raw = '';
        foreach($data as $item){
            $raw .= $item->key.'='.$item->value.PHP_EOL;
        }

        $server = Server::whereId($server_id)->first();
        $server->env_raw = $raw;
        $server->save();

        return redirect()->back();
    }
}
