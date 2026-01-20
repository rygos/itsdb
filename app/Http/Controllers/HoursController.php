<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
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
        $dailyHours = collect();
        $totalHours = 0;
        $averageHours = 0;
        $maxDailyHours = 0;
        if ($selectedYear) {
            $projects = Project::whereNotNull('end_date')
                ->whereHas('status', function ($query) {
                    $query->where('name', 'FINISHED');
                })
                ->whereYear('end_date', $selectedYear)
                ->orderBy('end_date')
                ->get();

            $dailyTotals = [];
            foreach ($projects as $project) {
                $dateKey = Carbon::parse($project->end_date)->toDateString();
                $hours = (int) ($project->hours ?? 0);
                $dailyTotals[$dateKey] = ($dailyTotals[$dateKey] ?? 0) + $hours;
            }

            $dailyHours = collect($dailyTotals)->sortKeys();
            $totalHours = $dailyHours->sum();
            $daysWithHours = max(1, $dailyHours->count());
            $averageHours = $totalHours / $daysWithHours;
            $maxDailyHours = max(1, (int) $dailyHours->max());
        }

        return view('hours.index', [
            'projects' => $projects,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'dailyHours' => $dailyHours,
            'totalHours' => $totalHours,
            'averageHours' => $averageHours,
            'maxDailyHours' => $maxDailyHours,
        ]);
    }
}
