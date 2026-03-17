<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestoreDatabaseCommand extends Command
{
    protected $signature = 'itsdb:restore {backup_path? : Optionaler Pfad zur Backup-Datei}';

    protected $description = 'Stellt die aktuelle Datenbank aus einem Backup wieder her';

    public function handle(): int
    {
        $backupPath = $this->argument('backup_path') ?: $this->latestBackupPath();
        if (!$backupPath || !File::exists($backupPath)) {
            $this->error('Keine Backup-Datei gefunden.');

            return self::FAILURE;
        }

        $connectionName = config('database.default');
        $connection = config("database.connections.$connectionName", []);
        $driver = $connection['driver'] ?? null;

        if ($driver === 'sqlite') {
            $restored = $this->restoreSqlite($connection, $backupPath);
        } elseif ($driver === 'mysql') {
            $restored = $this->restoreMysql($connection, $backupPath);
        } else {
            $this->error('Restore wird aktuell nur fuer SQLite und MariaDB/MySQL unterstuetzt.');

            return self::FAILURE;
        }

        if (!$restored) {
            return self::FAILURE;
        }

        $this->info('Datenbank wiederhergestellt aus: ' . $backupPath);

        return self::SUCCESS;
    }

    private function latestBackupPath(): ?string
    {
        $backupDirectory = storage_path('app/itsdb-backups');
        if (!File::isDirectory($backupDirectory)) {
            return null;
        }

        $files = collect(File::files($backupDirectory))
            ->filter(fn ($file) => in_array($file->getExtension(), ['sqlite', 'sql'], true))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        return $files->first()?->getPathname();
    }

    private function restoreSqlite(array $connection, string $backupPath): bool
    {
        $databasePath = $connection['database'] ?? null;
        if (!$databasePath || $databasePath === ':memory:') {
            $this->error('SQLite-Datenbankdatei nicht konfiguriert.');

            return false;
        }

        File::copy($backupPath, $databasePath);

        return true;
    }

    private function restoreMysql(array $connection, string $backupPath): bool
    {
        $database = $connection['database'] ?? null;
        if (!$database) {
            $this->error('Datenbankname fuer MariaDB/MySQL fehlt.');

            return false;
        }

        $sql = File::get($backupPath);
        $statements = $this->splitSqlStatements($sql);

        try {
            DB::connection()->unprepared('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($statements as $statement) {
                if ($statement === '') {
                    continue;
                }

                DB::connection()->unprepared($statement);
            }
            DB::connection()->unprepared('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $exception) {
            $this->error('MariaDB/MySQL-Restore fehlgeschlagen: ' . $exception->getMessage());

            return false;
        }

        return true;
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $length = strlen($sql);
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escapeNext = false;

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];

            if ($escapeNext) {
                $buffer .= $char;
                $escapeNext = false;
                continue;
            }

            if ($char === '\\') {
                $buffer .= $char;
                $escapeNext = true;
                continue;
            }

            if ($char === "'" && !$inDoubleQuote) {
                $inSingleQuote = !$inSingleQuote;
                $buffer .= $char;
                continue;
            }

            if ($char === '"' && !$inSingleQuote) {
                $inDoubleQuote = !$inDoubleQuote;
                $buffer .= $char;
                continue;
            }

            if ($char === ';' && !$inSingleQuote && !$inDoubleQuote) {
                $trimmed = trim($buffer);
                if ($trimmed !== '' && !str_starts_with($trimmed, '--')) {
                    $statements[] = $trimmed;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        $trimmed = trim($buffer);
        if ($trimmed !== '' && !str_starts_with($trimmed, '--')) {
            $statements[] = $trimmed;
        }

        return $statements;
    }
}
