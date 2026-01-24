<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\City;
use App\Models\Customer;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerProjectController extends Controller
{
    public function add()
    {
        $country = [
            'de' => 'Deutschland',
            'at' => 'Österreich',
            'ch' => 'Schweiz',
            'lu' => 'Luxemburg',
        ];

        return view('customers_projects.add', [
            'countrys' => $country,
        ]);
    }

    public function lookup_customer(Request $request)
    {
        $shortNo = trim((string)$request->query('short_no'));
        if ($shortNo === '') {
            return response()->json(['found' => false]);
        }

        $customer = Customer::where('short_no', $shortNo)->with('city')->first();
        if (!$customer) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id' => $customer->id,
            'short_no' => $customer->short_no,
            'sap_no' => $customer->sap_no,
            'dynamics_no' => $customer->dynamics_no,
            'name' => $customer->name,
            'city' => $customer->city ? $customer->city->name : '',
            'country_code' => $customer->city ? $customer->city->country_code : '',
        ]);
    }

    public function lookup_city(Request $request)
    {
        $name = trim((string)$request->query('name'));
        if ($name === '') {
            return response()->json(['found' => false]);
        }

        $city = City::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        if (!$city) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id' => $city->id,
            'name' => $city->name,
            'country_code' => $city->country_code,
        ]);
    }

    public function store(Request $request)
    {
        $shortNo = $request->get('short_no');
        $customer = Customer::where('short_no', $shortNo)->first();

        if (!$customer) {
            $cityId = $request->get('city_id');
            $cityName = trim((string)$request->get('city_name'));
            if (!$cityId && $cityName !== '') {
                $city = City::whereRaw('LOWER(name) = ?', [strtolower($cityName)])->first();
                if (!$city) {
                    $city = new City;
                    $city->name = $cityName;
                    $city->country_code = $request->get('country_code');
                    $city->save();
                }
                $cityId = $city->id;
            }

            $customer = new Customer;
            $customer->user_id = auth()->id();
            $customer->short_no = $shortNo;
            $customer->sap_no = $request->get('sap_no');
            $customer->dynamics_no = $request->get('customer_dynamics_no');
            $customer->name = $request->get('customer_name');
            $customer->city_id = $cityId;
            $customer->save();

            LogHelper::log('customer', $customer->id, 'Add', 'Create Customer: '.$customer->name);
        }

        $project = new Project;
        $project->dynamics_id = $request->get('project_dynamics_id');
        $project->name = $request->get('project_name');
        $project->customer_id = $customer->id;
        $project->user_id = auth()->id();
        $project->status_id = 1;
        $project->start_date = Carbon::parse($request->get('start_date').' 00:00');
        $project->end_date = Carbon::parse($request->get('end_date').' 00:00');
        $project->hours = $request->get('hours');
        $project->save();

        LogHelper::log('customer', $project->customer_id, 'Project', 'Add: '.$project->dynamics_id.' - '.$project->name);
        LogHelper::log('project', $project->id, 'Add', 'Add: '.$project->dynamics_id.' - '.$project->name);

        return redirect()->route('index');
    }
}
