<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Vacation;
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
        $serviceDaysTarget = 139;
        $serviceDaysCompleted = 0;
        $serviceDaysCompletionPercent = 0;
        $serviceDaysRemaining = $serviceDaysTarget;
        $daysConsideredForAverage = 0;
        $projectCompletionDates = [];
        $absenceChartData = [];
        $excludedAverageDates = [];
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
            $serviceDaysCompleted = $totalHours / 8;
            $serviceDaysCompletionPercent = $serviceDaysTarget > 0
                ? ($serviceDaysCompleted / $serviceDaysTarget) * 100
                : 0;
            $serviceDaysRemaining = max(0, $serviceDaysTarget - $serviceDaysCompleted);
            $absenceChartData = $this->buildAbsenceChartData($selectedYear);
            $excludedAverageDates = collect($absenceChartData)
                ->filter(fn (array $absence): bool => in_array($absence['type'], [Vacation::TYPE_VACATION, Vacation::TYPE_SICKNESS], true))
                ->keys()
                ->values()
                ->all();
            $projectCompletionDates = $projects
                ->pluck('end_date')
                ->filter()
                ->map(fn ($endDate) => Carbon::parse($endDate)->toDateString())
                ->unique()
                ->values()
                ->all();

            $daysConsideredForAverage = $this->count_average_days($startDate, $endDate, $projectCompletionDates, $excludedAverageDates);
            $averageHours = $daysConsideredForAverage > 0 ? $totalHours / $daysConsideredForAverage : 0;
            $maxDailyHours = max(1, (int) $dailyHours->max());
            if ((int) $selectedYear === (int) Carbon::now()->year) {
                $fullYearAbsenceData = $this->buildAbsenceChartData($selectedYear, false);
                $forecastExcludedDates = collect($fullYearAbsenceData)
                    ->filter(fn (array $absence): bool => $absence['type'] === Vacation::TYPE_VACATION)
                    ->keys()
                    ->values()
                    ->all();

                // Current-year forecast extrapolates the average onto the remaining working days.
                $fullYearAverageDays = $this->count_average_days(
                    Carbon::create($selectedYear, 1, 1)->startOfDay(),
                    Carbon::create($selectedYear, 12, 31)->endOfDay(),
                    $projectCompletionDates,
                    $forecastExcludedDates
                );
                $forecastHours = $averageHours * $fullYearAverageDays;
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
            'serviceDaysTarget' => $serviceDaysTarget,
            'serviceDaysCompleted' => $serviceDaysCompleted,
            'serviceDaysCompletionPercent' => $serviceDaysCompletionPercent,
            'serviceDaysRemaining' => $serviceDaysRemaining,
            'daysConsideredForAverage' => $daysConsideredForAverage,
            'projectCompletionDates' => $projectCompletionDates,
            'absenceChartData' => $absenceChartData,
            'excludedAverageDates' => $excludedAverageDates,
            'maxDailyHours' => $maxDailyHours,
            'forecastServiceDays' => $forecastServiceDays,
        ]);
    }

    private function buildAbsenceChartData(int|string $selectedYear, bool $limitToToday = true): array
    {
        $startDate = Carbon::create((int) $selectedYear, 1, 1)->startOfDay();
        $endDate = Carbon::create((int) $selectedYear, 12, 31)->endOfDay();
        if ($limitToToday && (int) $selectedYear === (int) Carbon::now()->year) {
            $endDate = Carbon::now()->endOfDay();
        }

        $vacations = Vacation::query()
            ->where('user_id', auth()->id())
            ->whereDate('start_date', '<=', $endDate->toDateString())
            ->whereDate('end_date', '>=', $startDate->toDateString())
            ->orderBy('start_date')
            ->get();

        $chartData = [];
        foreach ($vacations as $vacation) {
            $cursor = Carbon::parse($vacation->start_date)->max($startDate)->startOfDay();
            $vacationEnd = Carbon::parse($vacation->end_date)->min($endDate)->startOfDay();
            $holidays = $this->nrw_holidays((int) $cursor->year, (int) $vacationEnd->year);

            while ($cursor->lte($vacationEnd)) {
                $dateKey = $cursor->toDateString();
                if ($cursor->isWeekend() || in_array($dateKey, $holidays, true)) {
                    $cursor->addDay();
                    continue;
                }

                $hours = 8;
                $isSingleDay = $vacation->start_date->toDateString() === $vacation->end_date->toDateString();
                if ($isSingleDay) {
                    $hours = ($vacation->start_day_portion === Vacation::PORTION_HALF || $vacation->end_day_portion === Vacation::PORTION_HALF) ? 4 : 8;
                } elseif ($dateKey === $vacation->start_date->toDateString() && $vacation->start_day_portion === Vacation::PORTION_HALF) {
                    $hours = 4;
                } elseif ($dateKey === $vacation->end_date->toDateString() && $vacation->end_day_portion === Vacation::PORTION_HALF) {
                    $hours = 4;
                }

                $chartData[$dateKey] = [
                    'type' => $vacation->type,
                    'hours' => $hours,
                ];

                $cursor->addDay();
            }
        }

        return $chartData;
    }

    private function count_average_days(Carbon $startDate, Carbon $endDate, array $projectCompletionDates, array $excludedAverageDates): int
    {
        // Averages use weekdays plus weekend dates that actually had finished projects.
        $holidays = $this->nrw_holidays((int)$startDate->year, (int)$endDate->year);
        $projectCompletionDateLookup = array_fill_keys($projectCompletionDates, true);
        $excludedAverageDateLookup = array_fill_keys($excludedAverageDates, true);
        $days = 0;
        $cursor = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $dateKey = $cursor->toDateString();
            $isWeekend = $cursor->isWeekend();
            $isHoliday = in_array($dateKey, $holidays, true);
            $hasProjectCompletion = isset($projectCompletionDateLookup[$dateKey]);
            $isExcludedAverageDate = isset($excludedAverageDateLookup[$dateKey]);

            if (!$isExcludedAverageDate && ((!$isWeekend && !$isHoliday) || ($isWeekend && $hasProjectCompletion))) {
                $days++;
            }
            $cursor->addDay();
        }

        return $days;
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
