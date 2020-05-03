<?php

namespace Tests;

use Illuminate\Database\MigrationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            MigrationServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
        ];
    }
}
