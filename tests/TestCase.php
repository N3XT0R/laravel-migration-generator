<?php

namespace Tests;

use Illuminate\Foundation\Application;
use N3XT0R\MigrationGenerator\Providers\MigrationGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $resourceFolder = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->resourceFolder = __DIR__.'/Resources/';
    }

    /**
     * @param  Application  $app
     * @return array|string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            MigrationGeneratorServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $host = env('DB_HOST', 'db_migration');
        $app['config']->set('database.connections.mysql.host', $host);
        $app['config']->set('database.default', env('DB_CONNECTION', 'mysql'));
        $app['config']->set(
            'database.connections.mysql',
            [
                'host' => env('DB_HOST', 'db_migration'),
                'driver' => 'mysql',
                'database' => 'testing',
                'username' => 'root',
                'password' => '',
                'prefix' => '',
            ]
        );
        $app['config']->set(
            'database.connections.pgsql',
            [
                'host' => env('DB_HOST', 'db_migration'),
                'driver' => 'pgsql',
                'database' => 'testing',
                'username' => env('DB_USERNAME', 'postgres'),
                'password' => env('DB_PASSWORD', '!'),
                'prefix' => '',
            ]
        );
        $app['config']->set(
            'database.connections.sqlsrv',
            [
                'host' => env('DB_HOST', 'db_migration'),
                'driver' => 'sqlsrv',
                'database' => 'testing',
                'username' => env('DB_USERNAME', 'SA'),
                'password' => env('DB_PASSWORD', 'Passw0rd1234!'),
                'prefix' => '',
            ]
        );
    }
}
