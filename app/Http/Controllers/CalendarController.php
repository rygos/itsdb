<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request, $year = null, $month = null)
    {
        // Fall back to the current month when the route is opened without explicit year/month values.
        if (empty($year) || empty($month)) {
            $date = Carbon::now();
        } else {
            $date = Carbon::parse('01.'.$month.'.'.$year);
        }

        $start_of_calendar = $date->copy()->firstOfMonth()->startOfWeek(Carbon::MONDAY);
        $end_of_calendar = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SUNDAY);

        $day_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        // Use PHP date extraction instead of SQL YEAR() so test and production databases behave the same.
        $years = Project::query()
            ->ownedBy(auth()->id())
            ->whereNotNull('end_date')
            ->pluck('end_date')
            ->filter()
            ->mapWithKeys(function ($endDate) {
                $year = Carbon::parse($endDate)->year;

                return [$year => $year];
            })
            ->sortKeysDesc();

        // Count projects per day in memory for the same database portability reason.
        $projectCounts = Project::query()
            ->ownedBy(auth()->id())
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$start_of_calendar->copy()->startOfDay(), $end_of_calendar->copy()->endOfDay()])
            ->pluck('end_date')
            ->filter()
            ->countBy(fn ($endDate) => Carbon::parse($endDate)->toDateString());

        $selectedDay = $request->query('day');
        $selectedProjects = collect();
        if ($selectedDay) {
            // The detail table is loaded lazily only when the user drills into a specific day.
            $selectedProjects = Project::query()
                ->with(['customer.city', 'status'])
                ->ownedBy(auth()->id())
                ->whereNotNull('end_date')
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
