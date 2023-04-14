<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function view($id){
        $project = Project::whereId($id)->first();

        return view('projects.view', [
            'project' => $project,
        ]);
    }

    public function add(){
        $c = Customer::orderBy('short_no')->get();
        $customers = array();
        foreach ($c as $i){
            $customers[$i->id] = '('.$i->short_no.') '.$i->city->name.' - '.$i->name;
        }
        return view('projects.add', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request){
        $p = new Project;
        $p->dynamics_id = $request->get('dynamics_id');
        $p->name = $request->get('name');
        $p->customer_id = $request->get('customer');
        $p->user_id = 1;
        $p->status_id = 1;
        $p->save();

        return redirect()->route('index');
    }

    public function change_status(Request $request){
        $project = Project::whereId($request->get('project_id'))->first();
        $project->status_id = $request->get('status');
        $project->save();

        return redirect()->back();
    }
}
