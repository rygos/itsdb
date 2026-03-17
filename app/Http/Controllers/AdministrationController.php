<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdministrationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->hasPermission('administration', 'visible'), 403);

        $tab = $request->get('tab', 'users');
        $subtab = $request->get('subtab', 'import');

        return view('administration.index', [
            'tab' => $tab,
            'subtab' => $subtab,
            'users' => User::query()->orderBy('name')->get(),
            'statuses' => Status::query()->orderBy('name')->get(),
            'registrationEnabled' => AppSetting::getBoolean('registration_enabled', config('app.registration_enabled')),
            'permissionAreas' => User::permissionAreas(),
            'permissionLevels' => User::permissionLevels(),
        ]);
    }

    public function editUser(Request $request, User $user): View
    {
        abort_unless($request->user()?->hasPermission('administration', 'administration'), 403);

        return view('administration.user-edit', [
            'editUser' => $user,
            'permissionAreas' => User::permissionAreas(),
            'permissionLevels' => User::permissionLevels(),
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'administration'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        foreach (array_keys(User::permissionAreas()) as $area) {
            $user->{User::permissionColumn($area)} = User::resolvePermissionLevel($request->input("permissions.$area", []));
        }

        $user->save();

        return redirect()
            ->route('administration.index', ['tab' => 'users'])
            ->with('status', 'Benutzer aktualisiert.');
    }

    public function storeStatus(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:status,name'],
        ]);

        Status::create($validated);

        return redirect()
            ->route('administration.index', ['tab' => 'statuses'])
            ->with('status', 'Status angelegt.');
    }

    public function updateStatus(Request $request, Status $status): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'editable'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('status', 'name')->ignore($status->id)],
        ]);

        $status->update($validated);

        return redirect()
            ->route('administration.index', ['tab' => 'statuses'])
            ->with('status', 'Status aktualisiert.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('administration', 'administration'), 403);

        $registrationEnabled = $request->boolean('registration_enabled');

        AppSetting::put('registration_enabled', $registrationEnabled ? '1' : '0');
        config(['app.registration_enabled' => $registrationEnabled]);

        return redirect()
            ->route('administration.index', ['tab' => 'administration', 'subtab' => 'settings'])
            ->with('status', 'Einstellungen gespeichert.');
    }
}
