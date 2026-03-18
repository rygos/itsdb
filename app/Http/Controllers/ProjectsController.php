<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Customer;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectsController extends Controller
{
    public function view(Project $project): View
    {
        // Permissions are derived from ownership; eager loading keeps the detail page query count stable.
        $project->load(['customer.city', 'status', 'user']);
        $canManageProject = $project->user_id === auth()->id();

        return view('projects.view', [
            'project' => $project,
            'canManageProject' => $canManageProject,
        ]);
    }

    public function add(): View
    {
        // Build user-friendly customer labels once so the Blade view stays dumb.
        $customers = Customer::query()
            ->with('city')
            ->orderBy('short_no')
            ->get()
            ->mapWithKeys(fn (Customer $customer): array => [
                $customer->id => sprintf(
                    '(%s) %s - %s',
                    $customer->short_no,
                    $customer->city?->name ?? 'Kein Ort',
                    $customer->name
                ),
            ])
            ->all();

        return view('projects.add', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProjectPayload($request);

        $project = Project::query()->create([
            // Persist normalized values only; dates are stored at start-of-day because the UI is date-based.
            'dynamics_id' => trim($validated['dynamics_id']),
            'name' => trim($validated['name']),
            'customer_id' => (int) $validated['customer'],
            'user_id' => (int) $request->user()->id,
            'status_id' => 1,
            'start_date' => Carbon::parse($validated['start_date'])->startOfDay(),
            'end_date' => Carbon::parse($validated['end_date'])->startOfDay(),
            'hours' => $validated['hours'] ?? null,
        ]);

        LogHelper::log('customer', $project->customer_id, 'Project', 'Add: '.$project->dynamics_id.' - '.$project->name);
        LogHelper::log('project', $project->id, 'Add', 'Add: '.$project->dynamics_id.' - '.$project->name);

        return redirect()->route('index');
    }

    public function change_status(Request $request): RedirectResponse
    {
        // Status changes are validated separately because older forms submit only project/status ids.
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'status' => ['required', 'integer', 'exists:status,id'],
        ]);

        $project = Project::query()->with('status')->findOrFail($validated['project_id']);
        abort_unless($project->user_id === auth()->id(), 403);

        $project->status_id = $validated['status'];
        $project->save();
        $project->load('status');

        LogHelper::log('customer', $project->customer_id, 'Project', 'Change Status of '.$project->dynamics_id.' - '.$project->name.' to '.$project->status->name);
        LogHelper::log('project', $project->id, 'Status', 'Change Status of '.$project->dynamics_id.' - '.$project->name.' to '.$project->status->name);

        return redirect()->back();
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $this->validateProjectPayload($request, true);
        $project = Project::query()->findOrFail($validated['id']);
        abort_unless($project->user_id === auth()->id(), 403);

        $project->start_date = Carbon::parse($validated['start_date'])->startOfDay();
        $project->end_date = Carbon::parse($validated['end_date'])->startOfDay();
        $project->name = trim($validated['name']);
        $project->dynamics_id = trim($validated['dynamics_id']);
        $project->hours = $validated['hours'] ?? null;
        $project->save();

        return redirect()->back();
    }

    private function validateProjectPayload(Request $request, bool $requireId = false): array
    {
        // One shared validation method keeps create and update rules aligned.
        return $request->validate([
            'id' => [$requireId ? 'required' : 'nullable', 'integer', 'exists:projects,id'],
            'dynamics_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'customer' => [$requireId ? 'nullable' : 'required', 'integer', 'exists:customers,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'hours' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
