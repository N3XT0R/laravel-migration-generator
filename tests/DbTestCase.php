<?php


namespace Tests;


class DbTestCase extends TestCase
{


    protected array $migrations = [];

    protected function setUp(): void
    {
        parent::setUp();
        $migrations = $this->getMigrations();
        foreach ($migrations as $migrationPath) {
            $this->loadMigrationsFrom($migrationPath);
        }

        $this->loadLaravelMigrations(['--database' => env('DB_CONNECTION', 'mysql')]);
    }

    public function getMigrations(): array
    {
        if (0 === count($this->migrations)) {
            $this->setMigrations([
                $this->resourceFolder . 'Database/Migrations/Default/',
            ]);
        }
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