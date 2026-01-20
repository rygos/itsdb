<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $last5customers = Customer::orderByDesc('id')->limit(5)->get();
        $open_projects = Project::where('user_id', \Auth::id())
            ->whereHas('status', function ($query) {
                $query->where('name', '!=', 'FINISHED');
            })
            ->orderByDesc('id')
            ->get();

        return view('index.index', [
            'last5customers' => $last5customers,
            'open_projects' => $open_projects,
        ]);
    }
}
