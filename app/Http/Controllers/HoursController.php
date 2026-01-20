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

            $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
            if ((int) $selectedYear === (int) Carbon::now()->year) {
                $endDate = Carbon::now()->endOfDay();
            }

            $dailyHours = collect();
            $cursor = $startDate->copy();
            while ($cursor->lte($endDate)) {
                $dateKey = $cursor->toDateString();
                $dailyHours->put($dateKey, (int) ($dailyTotals[$dateKey] ?? 0));
                $cursor->addDay();
            }

            $totalHours = $dailyHours->sum();
            $daysInRange = max(1, $dailyHours->count());
            $averageHours = $totalHours / $daysInRange;
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
