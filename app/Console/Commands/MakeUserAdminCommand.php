<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdminCommand extends Command
{
    protected $signature = 'users:makeadmin {user_id : Die ID des Benutzers}';

    protected $description = 'Setzt das Administrationsrecht fuer die Benutzerverwaltung auf Administration';

    public function handle(): int
    {
        $user = User::query()->find($this->argument('user_id'));

        if (!$user) {
            $this->error('Benutzer nicht gefunden.');

            return self::FAILURE;
        }

        $user->permission_administration = User::PERMISSION_ADMINISTRATION;
        $user->save();

        $this->info(sprintf(
            'Benutzer %s (ID %d) kann jetzt in der Benutzerverwaltung Rechte aendern.',
            $user->name,
            $user->id
        ));

        return self::SUCCESS;
    }
}
