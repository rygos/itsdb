<?php

namespace App\Http\Controllers;

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

        return redirect()->back();
    }

    public function delete($id){
        $c = Credential::whereId($id)->first();
        $c->delete();

        return redirect()->back();
    }
}
