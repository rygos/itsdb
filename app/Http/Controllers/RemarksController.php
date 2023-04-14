<?php

namespace App\Http\Controllers;

use App\Models\Remark;
use Illuminate\Http\Request;

class RemarksController extends Controller
{
    public function store(Request $request){
        $r = Remark::whereType(1)->where('relation_id', $request->get('customer_id'))->first();

        if($r){
            $r->remark = $request->get('remark');
            $r->save();
        }else{
            $rin = new Remark;
            $rin->type = 1;
            $rin->remark = $request->get('remark');
            $rin->relation_id = $request->get('customer_id');
            $rin->save();
        }

        return redirect()->route('customers.view', $request->get('customer_id'));
    }
}
