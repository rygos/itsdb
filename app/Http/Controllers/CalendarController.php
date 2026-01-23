<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Project;

class CalendarController extends Controller
{
    public function index(Request $request, $year = null, $month = null){
        if(empty($year) or empty($month)){
            $date = Carbon::now();
        }else{
            $date = Carbon::parse('01.'.$month.'.'.$year);
        }

        $start_of_calendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
        $end_of_calendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);

        $day_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $years = Project::whereNotNull('end_date')
            ->selectRaw('YEAR(end_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year');

        $projectCounts = Project::whereNotNull('end_date')
            ->whereBetween('end_date', [$start_of_calendar->copy()->startOfDay(), $end_of_calendar->copy()->endOfDay()])
            ->selectRaw('DATE(end_date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $selectedDay = $request->query('day');
        $selectedProjects = collect();
        if ($selectedDay) {
            $selectedProjects = Project::whereNotNull('end_date')
                ->whereDate('end_date', $selectedDay)
                ->orderBy('end_date')
                ->get();
        }

        return view('calendar.index', [
            'date' => $date,
            'day_labels' => $day_labels,
            'start_of_calendar' => $start_of_calendar,
            'end_of_calendar' => $end_of_calendar,
            'years' => $years,
            'projectCounts' => $projectCounts,
            'selectedDay' => $selectedDay,
            'selectedProjects' => $selectedProjects,
        ]);
    }
}
