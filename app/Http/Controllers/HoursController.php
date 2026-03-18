<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    public function index(Request $request)
    {
        // Derive available years in PHP instead of database-specific YEAR() SQL so the logic
        // works across SQLite in tests and MySQL in production.
        $years = Project::query()
            ->ownedBy(auth()->id())
            ->whereNotNull('end_date')
            ->whereHas('status', function ($query) {
                $query->where('name', 'FINISHED');
            })
            ->pluck('end_date')
            ->filter()
            ->mapWithKeys(function ($endDate) {
                $year = Carbon::parse($endDate)->year;

                return [$year => $year];
            })
            ->sortKeysDesc();

        $selectedYear = $request->get('year', $years->first());

        $projects = collect();
        $dailyHours = collect();
        $totalHours = 0;
        $averageHours = 0;
        $maxDailyHours = 0;
        $forecastServiceDays = 0;
        if ($selectedYear) {
            // Only finished projects contribute to the hours overview.
            $projects = Project::query()
                ->with(['customer.city', 'status'])
                ->ownedBy(auth()->id())
                ->whereNotNull('end_date')
                ->whereHas('status', function ($query) {
                    $query->where('name', 'FINISHED');
                })
                ->whereYear('end_date', $selectedYear)
                ->orderBy('end_date')
                ->get();

            $dailyTotals = [];
            foreach ($projects as $project) {
                // Aggregate by finished date because the chart is day-based, not project-based.
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
            $workingDays = $this->count_working_days($startDate, $endDate);
            $averageHours = $workingDays > 0 ? $totalHours / $workingDays : 0;
            $maxDailyHours = max(1, (int) $dailyHours->max());
            if ((int) $selectedYear === (int) Carbon::now()->year) {
                // Current-year forecast extrapolates the average onto the remaining working days.
                $fullYearWorkingDays = $this->count_working_days(
                    Carbon::create($selectedYear, 1, 1)->startOfDay(),
                    Carbon::create($selectedYear, 12, 31)->endOfDay()
                );
                $forecastHours = $averageHours * $fullYearWorkingDays;
                $forecastServiceDays = $forecastHours / 8;
            } else {
                $forecastServiceDays = $totalHours / 8;
            }
        }

        return view('hours.index', [
            'projects' => $projects,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'dailyHours' => $dailyHours,
            'totalHours' => $totalHours,
            'averageHours' => $averageHours,
            'maxDailyHours' => $maxDailyHours,
            'forecastServiceDays' => $forecastServiceDays,
        ]);
    }

    private function count_working_days(Carbon $startDate, Carbon $endDate): int
    {
        // Holiday handling is centralized here so the dashboard calculation stays deterministic.
        $holidays = $this->nrw_holidays((int)$startDate->year, (int)$endDate->year);
        $workingDays = 0;
        $cursor = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $dateKey = $cursor->toDateString();
            $isWeekend = $cursor->isWeekend();
            if (!$isWeekend && !in_array($dateKey, $holidays, true)) {
                $workingDays++;
            }
            $cursor->addDay();
        }

        return $workingDays;
    }

    private function nrw_holidays(int $startYear, int $endYear): array
    {
        $dates = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $easter = $this->easter_sunday($year);

            $fixed = [
                Carbon::create($year, 1, 1),   // Neujahr
                Carbon::create($year, 5, 1),   // Tag der Arbeit
                Carbon::create($year, 10, 3),  // Tag der Deutschen Einheit
                Carbon::create($year, 11, 1),  // Allerheiligen (NRW)
                Carbon::create($year, 12, 25), // 1. Weihnachtstag
                Carbon::create($year, 12, 26), // 2. Weihnachtstag
            ];

            $moveable = [
                $easter->copy()->subDays(2),  // Karfreitag
                $easter->copy()->addDay(),    // Ostermontag
                $easter->copy()->addDays(39), // Christi Himmelfahrt
                $easter->copy()->addDays(50), // Pfingstmontag
                $easter->copy()->addDays(60), // Fronleichnam (NRW)
            ];

            foreach (array_merge($fixed, $moveable) as $holiday) {
                $dates[] = $holiday->toDateString();
            }
        }

        return array_values(array_unique($dates));
    }

    private function easter_sunday(int $year): Carbon
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day);
    }
}
