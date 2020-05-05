<?php


namespace Tests;


class DbTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/Resources/Database/Migrations/');
    }
}