<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    public function index(Request $request)
    {
        $years = Project::whereNotNull('end_date')
            ->whereHas('status', function ($query) {
                $query->where('name', 'FINISHED');
            })
            ->selectRaw('YEAR(end_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year');

        $selectedYear = $request->get('year', $years->first());

        $projects = collect();
        if ($selectedYear) {
            $projects = Project::whereNotNull('end_date')
                ->whereHas('status', function ($query) {
                    $query->where('name', 'FINISHED');
                })
                ->whereYear('end_date', $selectedYear)
                ->orderBy('end_date')
                ->get();
        }

        return view('hours.index', [
            'projects' => $projects,
            'years' => $years,
            'selectedYear' => $selectedYear,
        ]);
    }
}
