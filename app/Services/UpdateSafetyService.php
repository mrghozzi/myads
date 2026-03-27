<?php

namespace App\Services;

use App\Support\UpdatePreflightReport;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\DB;

class UpdateSafetyService
{
    private const DESTRUCTIVE_RULES = [
        'schema_drop' => '/Schema\s*::\s*dropIfExists\s*\(|Schema\s*::\s*drop\s*\(/i',
        'drop_column' => '/->\s*dropColumn\s*\(/i',
        'truncate_call' => '/->\s*truncate\s*\(|::\s*truncate\s*\(/i',
        'raw_delete' => '/\bdelete\s+from\b/i',
        'raw_truncate' => '/\btruncate\s+table\b/i',
        'raw_drop' => '/\bdrop\s+table\b/i',
    ];

    public function __construct(
        private readonly Migrator $migrator
    ) {
    }

    /**
     * @param  array<int, string>|null  $migrationPaths
     */
    public function run(?array $migrationPaths = null): UpdatePreflightReport
    {
        $connectionName = (string) config('database.default');
        $connectionConfig = (array) config("database.connections.{$connectionName}", []);
        $driver = (string) ($connectionConfig['driver'] ?? '');
        $database = $this->describeDatabase($connectionConfig);
        $checks = [];
        $pendingMigrations = [];
        $destructiveMigrations = [];
        $migrationPaths = $this->normalizeMigrationPaths($migrationPaths);

        try {
            DB::connection()->getPdo();

            $checks[] = $this->makeCheck(
                'database_connection',
                'passed',
                __('messages.update_preflight_connection'),
                __('messages.update_preflight_connection_ready', [
                    'connection' => $connectionName,
                    'driver' => $driver ?: 'unknown',
                    'database' => $database ?: 'unknown',
                ])
            );
        } catch (\Throwable $e) {
            $checks[] = $this->makeCheck(
                'database_connection',
                'failed',
                __('messages.update_preflight_connection'),
                __('messages.update_preflight_connection_failed', [
                    'message' => $e->getMessage(),
                ])
            );
        }

        $requiredPaths = [
            base_path(),
            storage_path('app'),
            base_path('bootstrap/cache'),
        ];

        $nonWritablePaths = array_values(array_filter(
            $requiredPaths,
            static fn (string $path): bool => ! is_dir($path) || ! is_writable($path)
        ));

        if ($nonWritablePaths === []) {
            $checks[] = $this->makeCheck(
                'filesystem',
                'passed',
                __('messages.update_preflight_filesystem'),
                __('messages.update_preflight_paths_writable')
            );
        } else {
            $checks[] = $this->makeCheck(
                'filesystem',
                'failed',
                __('messages.update_preflight_filesystem'),
                __('messages.update_preflight_paths_not_writable', [
                    'paths' => implode(', ', $nonWritablePaths),
                ])
            );
        }

        try {
            $repository = $this->migrator->getRepository();

            if (! $repository->repositoryExists()) {
                $checks[] = $this->makeCheck(
                    'pending_migrations',
                    'failed',
                    __('messages.update_preflight_pending_migrations'),
                    __('messages.update_preflight_migration_repository_missing')
                );
            } else {
                $migrationFiles = $this->migrator->getMigrationFiles($migrationPaths);
                $ranMigrations = $repository->getRan();
                $pendingFiles = array_values(array_filter(
                    $migrationFiles,
                    fn (string $file, string $migration): bool => ! in_array($migration, $ranMigrations, true),
                    ARRAY_FILTER_USE_BOTH
                ));

                $pendingMigrations = array_values(array_keys(array_filter(
                    $migrationFiles,
                    fn (string $file, string $migration): bool => ! in_array($migration, $ranMigrations, true),
                    ARRAY_FILTER_USE_BOTH
                )));

                $checks[] = $this->makeCheck(
                    'pending_migrations',
                    'passed',
                    __('messages.update_preflight_pending_migrations'),
                    $pendingMigrations === []
                        ? __('messages.update_preflight_no_pending_migrations')
                        : __('messages.update_preflight_pending_ready', [
                            'count' => count($pendingMigrations),
                        ])
                );

                $destructiveMigrations = $this->inspectMigrationFiles($pendingFiles, $pendingMigrations);

                $checks[] = $this->makeCheck(
                    'destructive_migrations',
                    $destructiveMigrations === [] ? 'passed' : 'failed',
                    __('messages.update_preflight_destructive_migrations'),
                    $destructiveMigrations === []
                        ? __('messages.update_preflight_no_destructive_migrations')
                        : __('messages.update_blocked_destructive_migration', [
                            'migrations' => implode(', ', array_map(
                                static fn (array $migration): string => $migration['migration'],
                                $destructiveMigrations
                            )),
                        ])
                );
            }
        } catch (\Throwable $e) {
            $checks[] = $this->makeCheck(
                'pending_migrations',
                'failed',
                __('messages.update_preflight_pending_migrations'),
                __('messages.update_preflight_check_failed', [
                    'message' => $e->getMessage(),
                ])
            );
        }

        return new UpdatePreflightReport(
            connectionName: $connectionName,
            driver: $driver,
            database: $database,
            checks: $checks,
            pendingMigrations: $pendingMigrations,
            destructiveMigrations: $destructiveMigrations,
            migrationPaths: $migrationPaths,
        );
    }

    /**
     * @param  array<int, string>  $files
     * @param  array<int, string>|null  $names
     * @return array<int, array{migration: string, matches: array<int, string>}>
     */
    public function inspectMigrationFiles(array $files, ?array $names = null): array
    {
        $destructiveMigrations = [];

        foreach (array_values($files) as $index => $file) {
            $matches = $this->inspectMigrationFile($file);

            if ($matches === []) {
                continue;
            }

            $destructiveMigrations[] = [
                'migration' => $names[$index] ?? pathinfo($file, PATHINFO_FILENAME),
                'matches' => $matches,
            ];
        }

        return $destructiveMigrations;
    }

    /**
     * @return array<int, string>
     */
    public function inspectMigrationFile(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        $upMethodBody = $this->extractMethodBody(file_get_contents($path) ?: '', 'up');

        if ($upMethodBody === '') {
            return [];
        }

        $matches = [];

        foreach (self::DESTRUCTIVE_RULES as $rule => $pattern) {
            if (preg_match($pattern, $upMethodBody) === 1) {
                $matches[] = $rule;
            }
        }

        return $matches;
    }

    /**
     * @return array{name: string, status: string, title: string, detail: string}
     */
    private function makeCheck(string $name, string $status, string $title, string $detail): array
    {
        return compact('name', 'status', 'title', 'detail');
    }

    /**
     * @param  array<int, string>|null  $migrationPaths
     * @return array<int, string>
     */
    private function normalizeMigrationPaths(?array $migrationPaths): array
    {
        if ($migrationPaths === null || $migrationPaths === []) {
            return [database_path('migrations')];
        }

        return array_values(array_filter($migrationPaths, static fn (string $path): bool => $path !== ''));
    }

    /**
     * @param  array<string, mixed>  $connectionConfig
     */
    private function describeDatabase(array $connectionConfig): ?string
    {
        $driver = (string) ($connectionConfig['driver'] ?? '');

        if ($driver === 'sqlite') {
            return (string) ($connectionConfig['database'] ?? '');
        }

        return (string) ($connectionConfig['database'] ?? '');
    }

    private function extractMethodBody(string $source, string $methodName): string
    {
        $tokens = token_get_all($source);
        $collecting = false;
        $braceDepth = 0;
        $body = '';
        $pendingFunction = false;

        foreach ($tokens as $token) {
            if (is_array($token)) {
                [$tokenId, $tokenValue] = $token;

                if ($tokenId === T_FUNCTION) {
                    $pendingFunction = true;
                    continue;
                }

                if ($pendingFunction && $tokenId === T_STRING) {
                    $collecting = $tokenValue === $methodName;
                    $pendingFunction = false;
                    continue;
                }

                if ($collecting && ! in_array($tokenId, [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    $body .= $tokenValue;
                }

                continue;
            }

            if (! $collecting) {
                continue;
            }

            if ($token === '{') {
                $braceDepth++;

                if ($braceDepth === 1) {
                    continue;
                }
            }

            if ($token === '}') {
                $braceDepth--;

                if ($braceDepth === 0) {
                    break;
                }
            }

            if ($braceDepth >= 1) {
                $body .= $token;
            }
        }

        return $body;
    }
}
