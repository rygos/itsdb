<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'itsdb:backup';

    protected $description = 'Erstellt ein Backup der aktuellen Datenbank';

    public function handle(): int
    {
        $backupDirectory = storage_path('app/itsdb-backups');
        File::ensureDirectoryExists($backupDirectory);

        $connectionName = config('database.default');
        $connection = config("database.connections.$connectionName", []);
        $driver = $connection['driver'] ?? null;

        if ($driver === 'sqlite') {
            $backupPath = $this->backupSqlite($connection, $backupDirectory);
        } elseif ($driver === 'mysql') {
            $backupPath = $this->backupMysql($connection, $backupDirectory);
        } else {
            $this->error('Backup wird aktuell nur fuer SQLite und MariaDB/MySQL unterstuetzt.');

            return self::FAILURE;
        }

        if (!$backupPath) {
            return self::FAILURE;
        }

        $this->info('Backup erstellt: ' . $backupPath);

        return self::SUCCESS;
    }

    private function backupSqlite(array $connection, string $backupDirectory): ?string
    {
        $databasePath = $connection['database'] ?? null;
        if (!$databasePath || $databasePath === ':memory:' || !File::exists($databasePath)) {
            $this->error('SQLite-Datenbankdatei nicht gefunden.');

            return null;
        }

        $backupPath = $backupDirectory . '/itsdb-backup-' . now()->format('Ymd-His') . '.sqlite';
        File::copy($databasePath, $backupPath);

        return $backupPath;
    }

    private function backupMysql(array $connection, string $backupDirectory): ?string
    {
        $database = $connection['database'] ?? null;
        if (!$database) {
            $this->error('Datenbankname fuer MariaDB/MySQL fehlt.');

            return null;
        }

        $backupPath = $backupDirectory . '/itsdb-backup-' . now()->format('Ymd-His') . '.sql';
        $sql = $this->buildMysqlDump($database);
        File::put($backupPath, $sql);

        return $backupPath;
    }

    private function buildMysqlDump(string $database): string
    {
        $connection = DB::connection();
        $tableRows = $connection->select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;
        $tables = array_map(static fn ($row) => $row->{$tableKey}, $tableRows);

        $dump = [];
        $dump[] = '-- ITS-DB SQL Backup';
        $dump[] = '-- Generated at ' . now()->toDateTimeString();
        $dump[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $dump[] = '';

        foreach ($tables as $table) {
            $createRow = $connection->selectOne('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`');
            $createStatement = $createRow->{'Create Table'} ?? null;
            if (!$createStatement) {
                continue;
            }

            $dump[] = 'DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`;';
            $dump[] = $createStatement . ';';
            $dump[] = '';

            $rows = $connection->table($table)->get();
            foreach ($rows as $row) {
                $values = [];
                foreach ((array) $row as $value) {
                    $values[] = $this->quoteMysqlValue($value);
                }

                $dump[] = 'INSERT INTO `' . str_replace('`', '``', $table) . '` VALUES (' . implode(', ', $values) . ');';
            }

            if ($rows->isNotEmpty()) {
                $dump[] = '';
            }
        }

        $dump[] = 'SET FOREIGN_KEY_CHECKS=1;';
        $dump[] = '';

        return implode("\n", $dump);
    }

    private function quoteMysqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::connection()->getPdo()->quote((string) $value);
    }
}
