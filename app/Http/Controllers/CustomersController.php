<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\City;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Remark;
use App\Models\Status;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isNull;

class CustomersController extends Controller
{
    public function index(){
        $customers = Customer::orderBy('short_no')->get();

        return view('customers.index', [
            'customers' => $customers
        ]);
    }

    public function view($id){
        $customer = Customer::whereId($id)->first();
        $remark = Remark::whereType(1)->where('relation_id', $id)->first();
        $status = Status::get();

        foreach ($status as $item){
            $st[$item->id] = $item->name;
        }

        if(!$remark){
            $remark_ret = '';
        }else{
            $remark_ret = $remark->remark;
        }

        return view('customers.view', [
            'customer' => $customer,
            'remark' => $remark_ret,
            'status' => $st,
        ]);
    }

    public function add(){
        $citys = City::orderBy('name')->get();
        $citys_res = array();
        foreach ($citys as $item){
            $citys_res[$item->id] = strtoupper($item->country_code).' - '.$item->name;
        }

        $country = [
            'de' => 'Deutschland',
            'at' => 'Österreich',
            'ch' => 'Schweiz',
            'lu' => 'Luxemburg',
        ];

        return view('customers.add', [
            'citys' => $citys_res,
            'countrys' => $country,
        ]);
    }

    public function store(Request $request){
        $c = new Customer;
        $c->user_id = 1;
        //$c->type = $request->get('type');
        $c->short_no = $request->get('short_no');
        $c->sap_no = $request->get('sap_no');
        $c->dynamics_no = $request->get('dynamics_no');
        $c->name = $request->get('name');
        $c->city_id = $request->get('city');
        $c->save();

        LogHelper::log('customer', $c->id, 'Add', 'Create Customer: '.$c->name);

        return redirect()->route('index');
    }

    public function store_city(Request $request){
        $c = new City;
        $c->name = $request->get('name');
        $c->country_code = $request->get('country_code');
        $c->save();

        return redirect()->back();
    }

    public function contact_create(Request $request){
        $c = new CustomerContact;
        $c->customer_id = $request->get('customer_id');
        $c->prefix = $request->get('prefix');
        $c->name = $request->get('name');
        $c->familyname = $request->get('familyname');
        $c->phone_mobile = $request->get('phone_mobile');
        $c->phone_office = $request->get('phone_office');
        $c->email = $request->get('email');
        $c->comments = $request->get('comments');
        $c->save();

        LogHelper::log('customer', $c->customer_id, 'Contact', 'Contact Added: '.$c->name.' '.$c->familyname);

        return redirect()->route('customers.view', $c->customer_id);
    }

    public function contact_update(Request $request){
        $c = CustomerContact::whereId($request->get('id'))->first();
        $c->comments = $request->get('comments');
        $c->save();

        LogHelper::log('customer', $c->customer_id, 'Contact', 'Contact Comment for '.$c->name.' '.$c->familyname.' - '.$c->comments);

        return redirect()->back();

    }

    public function contact_delete($id){
        $contact = CustomerContact::whereId($id)->first();

        LogHelper::log('customer', $contact->customer_id, 'Contact', 'Delete Contact: '.$contact->name.' '.$contact->familyname);

        $contact->delete();

        return redirect()->back();
    }
}
