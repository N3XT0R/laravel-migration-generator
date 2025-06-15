<?php


namespace Tests;


class DbTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom($this->resourceFolder.'/Database/Migrations/');
        $this->loadLaravelMigrations(['--database' => 'mysql']);
    }

    protected function skipUnlessDatabase(string $engine): void
    {
        if (env('DB_CONNECTION') !== $engine) {
            $this->markTestSkipped("Skipped: Not running on $engine.");
        }
    }
}