<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    public function index(Request $request)
    {
        $years = Project::whereNotNull('end_date')
            ->where('status_id', 5)
            ->selectRaw('YEAR(end_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year');

        $currentYear = now()->year;
        $selectedYear = $request->get('year');
        if (!$selectedYear) {
            $selectedYear = $years->has($currentYear) ? $currentYear : $years->first();
        }

        $projects = collect();
        if ($selectedYear) {
            $projects = Project::whereNotNull('end_date')
                ->where('status_id', 5)
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
