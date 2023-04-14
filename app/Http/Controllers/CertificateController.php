<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Server;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function update(Request $request){
        $c = Customer::whereId($request->get('customer_id'))->first();
        $c->intermediate_cert_raw = $request->get('intermediate');
        $c->root_cert_raw = $request->get('root');
        $c->save();

        $s = Server::whereId($request->get('server_id'))->first();
        $s->server_cert_raw = $request->get('server');
        $s->private_key_raw = $request->get('private_key');
        $s->save();

        return redirect()->back();
    }
}
