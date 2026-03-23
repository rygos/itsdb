<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request, $year = null, $month = null): View
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
            ->all();

        $vacationYears = [];
        Vacation::query()
            ->where('user_id', auth()->id())
            ->get(['start_date', 'end_date'])
            ->each(function (Vacation $vacation) use (&$vacationYears): void {
                $startYear = Carbon::parse($vacation->start_date)->year;
                $endYear = Carbon::parse($vacation->end_date)->year;

                for ($currentYear = $startYear; $currentYear <= $endYear; $currentYear++) {
                    $vacationYears[$currentYear] = $currentYear;
                }
            });

        $years = collect($years + $vacationYears + [Carbon::now()->year => Carbon::now()->year])->sortKeysDesc();

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

        $vacationYearStart = Carbon::create((int) $date->format('Y'), 1, 1)->startOfDay();
        $vacationYearEnd = Carbon::create((int) $date->format('Y'), 12, 31)->endOfDay();
        $vacations = Vacation::query()
            ->where('user_id', auth()->id())
            ->whereDate('start_date', '<=', $vacationYearEnd->toDateString())
            ->whereDate('end_date', '>=', $vacationYearStart->toDateString())
            ->orderBy('start_date')
            ->get();

        return view('calendar.index', [
            'date' => $date,
            'day_labels' => $day_labels,
            'start_of_calendar' => $start_of_calendar,
            'end_of_calendar' => $end_of_calendar,
            'years' => $years,
            'projectCounts' => $projectCounts,
            'selectedDay' => $selectedDay,
            'selectedProjects' => $selectedProjects,
            'vacations' => $vacations,
        ]);
    }

    public function storeVacation(Request $request): RedirectResponse
    {
        $validated = $this->validateVacation($request);

        Vacation::query()->create([
            'user_id' => (int) $request->user()->id,
            'start_date' => Carbon::parse($validated['start_date'])->toDateString(),
            'end_date' => Carbon::parse($validated['end_date'])->toDateString(),
            'days' => $this->countVacationDays(
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->startOfDay()
            ),
        ]);

        return redirect()->route('calendar.index', [
            'year' => Carbon::parse($validated['start_date'])->format('Y'),
            'month' => Carbon::parse($validated['start_date'])->format('m'),
        ]);
    }

    public function updateVacation(Request $request, Vacation $vacation): RedirectResponse
    {
        abort_unless($vacation->user_id === (int) $request->user()->id, 403);
        $validated = $this->validateVacation($request);

        $vacation->start_date = Carbon::parse($validated['start_date'])->toDateString();
        $vacation->end_date = Carbon::parse($validated['end_date'])->toDateString();
        $vacation->days = $this->countVacationDays(
            Carbon::parse($validated['start_date'])->startOfDay(),
            Carbon::parse($validated['end_date'])->startOfDay()
        );
        $vacation->save();

        return redirect()->route('calendar.index', [
            'year' => Carbon::parse($validated['start_date'])->format('Y'),
            'month' => Carbon::parse($validated['start_date'])->format('m'),
        ]);
    }

    public function deleteVacation(Request $request, Vacation $vacation): RedirectResponse
    {
        abort_unless($vacation->user_id === (int) $request->user()->id, 403);

        $year = Carbon::parse($vacation->start_date)->format('Y');
        $month = Carbon::parse($vacation->start_date)->format('m');
        $vacation->delete();

        return redirect()->route('calendar.index', [
            'year' => $year,
            'month' => $month,
        ]);
    }

    private function validateVacation(Request $request): array
    {
        return $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);
    }

    private function countVacationDays(Carbon $startDate, Carbon $endDate): int
    {
        $holidays = $this->nrwHolidays((int) $startDate->year, (int) $endDate->year);
        $days = 0;
        $cursor = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $dateKey = $cursor->toDateString();
            if (!$cursor->isWeekend() && !in_array($dateKey, $holidays, true)) {
                $days++;
            }

            $cursor->addDay();
        }

        return $days;
    }

    private function nrwHolidays(int $startYear, int $endYear): array
    {
        $dates = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $easter = $this->easterSunday($year);

            $fixed = [
                Carbon::create($year, 1, 1),
                Carbon::create($year, 5, 1),
                Carbon::create($year, 10, 3),
                Carbon::create($year, 11, 1),
                Carbon::create($year, 12, 25),
                Carbon::create($year, 12, 26),
            ];

            $moveable = [
                $easter->copy()->subDays(2),
                $easter->copy()->addDay(),
                $easter->copy()->addDays(39),
                $easter->copy()->addDays(50),
                $easter->copy()->addDays(60),
            ];

            foreach (array_merge($fixed, $moveable) as $holiday) {
                $dates[] = $holiday->toDateString();
            }
        }

        return array_values(array_unique($dates));
    }

    private function easterSunday(int $year): Carbon
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
