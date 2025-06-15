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
        $defaultConnection = env('DB_CONNECTION', 'mysql');
        $host = env('DB_HOST', '127.0.0.1');

        $credentials = [
            'mysql' => ['username' => 'root', 'password' => ''],
            'pgsql' => ['username' => env('DB_USERNAME', 'postgres'), 'password' => env('DB_PASSWORD', '')],
            'sqlsrv' => ['username' => env('DB_USERNAME', 'SA'), 'password' => env('DB_PASSWORD', 'Passw0rd1234!')],
        ];

        $app['config']->set('database.default', $defaultConnection);

        if ($defaultConnection === 'pgsql') {
            dd([$credentials['pgsql'], $defaultConnection]);
        }

        $app['config']->set("database.connections.$defaultConnection", [
            'host' => $host,
            'driver' => $defaultConnection,
            'database' => 'testing',
            'username' => $credentials[$defaultConnection]['username'] ?? 'root',
            'password' => $credentials[$defaultConnection]['password'] ?? '',
            'prefix' => '',
        ]);
    }
}
