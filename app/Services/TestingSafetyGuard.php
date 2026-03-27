<?php

namespace App\Services;

use RuntimeException;

class TestingSafetyGuard
{
    public function ensureIsolated(): void
    {
        $defaultConnection = (string) config('database.default');
        $connection = (array) config("database.connections.{$defaultConnection}", []);
        $driver = (string) ($connection['driver'] ?? '');
        $database = (string) ($connection['database'] ?? '');
        $expectedDatabase = $this->normalizePath(database_path('testing.sqlite'));

        if ($driver === 'sqlite' && $this->normalizePath($database) === $expectedDatabase && ! file_exists($database)) {
            touch($database);
        }

        $isIsolatedTestingDatabase = $driver === 'sqlite'
            && $this->normalizePath($database) === $expectedDatabase;

        if (app()->environment('testing') && $isIsolatedTestingDatabase) {
            return;
        }

        throw new RuntimeException(__('messages.testing_database_guard_message', [
            'connection' => $defaultConnection ?: 'unknown',
            'driver' => $driver ?: 'unknown',
            'database' => $database !== '' ? $database : 'unknown',
            'expected' => database_path('testing.sqlite'),
        ]));
    }

    private function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
