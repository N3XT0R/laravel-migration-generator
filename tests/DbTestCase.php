<?php


namespace Tests;


use Illuminate\Foundation\Testing\RefreshDatabase;

class DbTestCase extends TestCase
{

    use RefreshDatabase;


    protected array $migrations = [
        'Database/Migrations/Default/',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $migrations = $this->getMigrations();
        foreach ($migrations as $migrationPath) {
            $this->loadMigrationsFrom($this->resourceFolder . $migrationPath);
        }

        $this->loadLaravelMigrations(['--database' => $this->getDatabaseFromEnv()]);
    }

    protected function getDatabaseFromEnv(): string
    {
        return (string)env('DB_CONNECTION', 'mysql');
    }

    public function getMigrations(): array
    {
        return $this->migrations;
    }

    public function setMigrations(array $migrations): void
    {
        $this->migrations = $migrations;
    }

    protected function skipUnlessDatabase(string $engine): void
    {
        if (env('DB_CONNECTION') !== $engine) {
            $this->markTestSkipped("Skipped: Not running on $engine.");
        }
    }
}