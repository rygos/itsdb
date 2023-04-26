<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Customer;
use App\Models\Project;
use Carbon\Carbon;
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
        $p->user_id = \Auth::id();
        $p->status_id = 1;
        $p->start_date = Carbon::parse($request->get('start_date').' '.$request->get('start_date_time'));
        $p->end_date = Carbon::parse($request->get('end_date').' '.$request->get('end_date_time'));
        $p->save();

        LogHelper::log('customer', $p->customer_id, 'Project', 'Add: '.$p->dynamics_id.' - '.$p->name);
        LogHelper::log('project', $p->id, 'Add', 'Add: '.$p->dynamics_id.' - '.$p->name);

        return redirect()->route('index');
    }

    public function change_status(Request $request){
        $project = Project::whereId($request->get('project_id'))->first();
        $project->status_id = $request->get('status');
        $project->save();

        LogHelper::log('customer', $project->customer_id, 'Project', 'Change Status of '.$project->dynamics_id.' - '.$project->name.' to '.$project->status->name);
        LogHelper::log('project', $project->id, 'Status', 'Change Status of '.$project->dynamics_id.' - '.$project->name.' to '.$project->status->name);

        return redirect()->back();
    }

    public function update(Request $request){
        $p = Project::whereId($request->get('id'))->first();
        $p->start_date = Carbon::parse($request->get('start_date').' '.$request->get('start_date_time'));
        $p->end_date = Carbon::parse($request->get('end_date').' '.$request->get('end_date_time'));
        $p->name = $request->get('name');
        $p->dynamics_id = $request->get('dynamics_id');
        $p->save();

        return redirect()->back();
    }
}
