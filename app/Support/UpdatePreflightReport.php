<?php

namespace App\Support;

class UpdatePreflightReport
{
    /**
     * @param  array<int, array{name: string, status: string, title: string, detail: string}>  $checks
     * @param  array<int, string>  $pendingMigrations
     * @param  array<int, array{migration: string, matches: array<int, string>}>  $destructiveMigrations
     * @param  array<int, string>  $migrationPaths
     */
    public function __construct(
        public readonly string $connectionName,
        public readonly string $driver,
        public readonly ?string $database,
        public readonly array $checks,
        public readonly array $pendingMigrations,
        public readonly array $destructiveMigrations,
        public readonly array $migrationPaths,
    ) {
    }

    public function isSafe(): bool
    {
        foreach ($this->checks as $check) {
            if (($check['status'] ?? 'failed') !== 'passed') {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, array{name: string, status: string, title: string, detail: string}>
     */
    public function failedChecks(): array
    {
        return array_values(array_filter(
            $this->checks,
            static fn (array $check): bool => ($check['status'] ?? 'failed') !== 'passed'
        ));
    }

    /**
     * @return array<int, string>
     */
    public function failureMessages(): array
    {
        return array_map(
            static fn (array $check): string => (string) ($check['detail'] ?? ''),
            $this->failedChecks()
        );
    }
}
