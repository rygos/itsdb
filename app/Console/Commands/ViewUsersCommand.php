<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ViewUsersCommand extends Command
{
    protected $signature = 'users:view';

    protected $description = 'Zeigt alle Benutzer mit IDs und Berechtigungsstufen an';

    public function handle(): int
    {
        $users = User::query()->orderBy('id')->get();

        if ($users->isEmpty()) {
            $this->warn('Keine Benutzer vorhanden.');

            return self::SUCCESS;
        }

        $headers = [
            'ID',
            'Username',
            'E-Mail',
            'Administration',
            'Produktematrix',
            'Compose',
            'Stunden',
            'Customers',
            'Projekte',
            'Calender',
        ];

        $rows = $users->map(function (User $user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $this->formatPermissionLevel($user->permission_administration),
                $this->formatPermissionLevel($user->permission_product_matrix),
                $this->formatPermissionLevel($user->permission_compose),
                $this->formatPermissionLevel($user->permission_hours),
                $this->formatPermissionLevel($user->permission_customers),
                $this->formatPermissionLevel($user->permission_projects),
                $this->formatPermissionLevel($user->permission_calendar),
            ];
        })->all();

        $this->table($headers, $rows);

        return self::SUCCESS;
    }

    private function formatPermissionLevel(int $level): string
    {
        return match ($level) {
            User::PERMISSION_VISIBLE => 'Sichtbar',
            User::PERMISSION_EDITABLE => 'Editierbar',
            User::PERMISSION_ADMINISTRATION => 'Administration',
            default => '-',
        };
    }
}
