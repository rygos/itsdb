<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Log;
use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use App\Models\Vacation;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserStatisticsController extends Controller
{
    public function show(int $user_id): View
    {
        $user = User::query()->findOrFail($user_id);
        $viewer = request()->user();

        abort_unless(
            $viewer !== null && ($viewer->is($user) || $viewer->hasPermission('administration', 'visible')),
            403
        );

        $projectsQuery = Project::query()
            ->with(['customer', 'status'])
            ->where('user_id', $user->id);

        $customersQuery = Customer::query()
            ->with('city')
            ->where('user_id', $user->id);

        $serversQuery = Server::query()
            ->with(['customer', 'serverKind', 'operatingSystem'])
            ->where('user_id', $user->id);

        $documentsQuery = CustomerDocument::query()
            ->with('customer')
            ->where('user_id', $user->id);

        $logsQuery = Log::query()
            ->where('user_id', $user->id);

        $vacationsQuery = Vacation::query()
            ->where('user_id', $user->id);

        $statistics = [
            'customers_count' => (clone $customersQuery)->count(),
            'projects_count' => (clone $projectsQuery)->count(),
            'open_projects_count' => (clone $projectsQuery)
                ->whereHas('status', fn (Builder $query): Builder => $query->where('name', '!=', 'FINISHED'))
                ->count(),
            'finished_projects_count' => (clone $projectsQuery)
                ->whereHas('status', fn (Builder $query): Builder => $query->where('name', 'FINISHED'))
                ->count(),
            'planned_hours' => (int) ((clone $projectsQuery)->sum('hours') ?? 0),
            'servers_count' => (clone $serversQuery)->count(),
            'documents_count' => (clone $documentsQuery)->count(),
            'documents_size' => (int) ((clone $documentsQuery)->sum('file_size') ?? 0),
            'logs_count' => (clone $logsQuery)->count(),
            'recent_logs_count' => (clone $logsQuery)->where('created_at', '>=', now()->subDays(30))->count(),
            'vacations_count' => (clone $vacationsQuery)->count(),
            'vacation_units' => (int) ((clone $vacationsQuery)->sum('day_units') ?? 0),
        ];

        $serverTypes = Server::query()
            ->selectRaw("COALESCE(NULLIF(type, ''), 'Nicht gesetzt') as label, COUNT(*) as aggregate")
            ->where('user_id', $user->id)
            ->groupBy('label')
            ->orderByDesc('aggregate')
            ->orderBy('label')
            ->get();

        $logSections = Log::query()
            ->selectRaw("COALESCE(NULLIF(section, ''), 'Ohne Bereich') as label, COUNT(*) as aggregate")
            ->where('user_id', $user->id)
            ->groupBy('label')
            ->orderByDesc('aggregate')
            ->orderBy('label')
            ->get();

        $logTypes = Log::query()
            ->selectRaw("COALESCE(NULLIF(type, ''), 'Ohne Typ') as label, COUNT(*) as aggregate")
            ->where('user_id', $user->id)
            ->groupBy('label')
            ->orderByDesc('aggregate')
            ->orderBy('label')
            ->get();

        $vacationTypes = Vacation::query()
            ->selectRaw('type, COUNT(*) as aggregate, COALESCE(SUM(day_units), 0) as day_units')
            ->where('user_id', $user->id)
            ->groupBy('type')
            ->orderByDesc('aggregate')
            ->get()
            ->map(function (Vacation $vacation): array {
                return [
                    'label' => Vacation::typeOptions()[$vacation->type] ?? $vacation->type,
                    'aggregate' => (int) $vacation->aggregate,
                    'day_units' => (int) $vacation->day_units,
                ];
            });

        return view('users.statistics', [
            'statsUser' => $user,
            'statistics' => $statistics,
            'permissions' => $this->permissionSummary($user),
            'serverTypes' => $serverTypes,
            'logSections' => $logSections,
            'logTypes' => $logTypes,
            'vacationTypes' => $vacationTypes,
            'recentProjects' => (clone $projectsQuery)->latest('created_at')->limit(5)->get(),
            'recentCustomers' => (clone $customersQuery)->latest('created_at')->limit(5)->get(),
            'recentServers' => (clone $serversQuery)->latest('created_at')->limit(5)->get(),
            'recentDocuments' => (clone $documentsQuery)->latest('created_at')->limit(5)->get(),
            'recentLogs' => (clone $logsQuery)->latest('created_at')->limit(10)->get(),
            'recentVacations' => (clone $vacationsQuery)->latest('start_date')->limit(10)->get(),
        ]);
    }

    private function permissionSummary(User $user): Collection
    {
        return collect(User::permissionAreas())
            ->map(function (string $label, string $area) use ($user): array {
                $level = $user->permissionLevel($area);

                return [
                    'area' => $label,
                    'level' => $level,
                    'label' => User::permissionLevels()[$level] ?? 'Keine Rechte',
                ];
            });
    }
}
