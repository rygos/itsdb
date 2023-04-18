<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Credential;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    public function store(Request $request){
        $c = new Credential;
        $c->customer_id = $request->get('customer_id');
        $c->type = $request->get('type');
        $c->user_id = 1;
        $c->username = $request->get('username');
        $c->password = $request->get('password');
        $c->save();

        LogHelper::log('customer', $c->customer_id, 'Credential', 'Create '.$c->type.' Credential for User: '.$c->username);

        return redirect()->back();
    }

    public function delete($id){
        $c = Credential::whereId($id)->first();

        LogHelper::log('customer', $c->customer_id, 'Credential', 'Delete '.$c->type.' Credential for User: '.$c->username);

        $c->delete();

        return redirect()->back();
    }
}
