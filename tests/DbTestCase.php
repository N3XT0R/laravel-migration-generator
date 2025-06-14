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
}