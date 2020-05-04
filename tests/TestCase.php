<?php

namespace Tests;

use N3XT0R\MigrationGenerator\Providers\MigrationGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Foundation\Application;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            MigrationGeneratorServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
        ];
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set(
            'database.connections.mysql',
            [
                'driver' => 'mysql',
                'database' => 'testing',
                'username' => 'root',
                'password' => '',
                'prefix' => '',
            ]
        );
    }
}
