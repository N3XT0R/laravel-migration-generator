<?php

namespace Tests;

use N3XT0R\MigrationGenerator\Providers\MigrationGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            MigrationGeneratorServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
        ];
    }
}
