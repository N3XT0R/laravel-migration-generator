<?php

namespace Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Illuminate\Database\DatabaseManager;
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

        $app['config']->set("database.connections.$defaultConnection", [
            'host' => $host,
            'driver' => $defaultConnection,
            'database' => 'testing',
            'username' => $credentials[$defaultConnection]['username'] ?? 'root',
            'password' => $credentials[$defaultConnection]['password'] ?? '',
            'prefix' => '',
        ]);
    }

    public function getDatabaseManager(): DatabaseManager
    {
        /**
         * @var DatabaseManager $dbManager
         */
        $dbManager = $this->app->get('db');
        return $dbManager;
    }


    public function getDoctrineConnection(DatabaseManager $dbManager): Connection
    {
        $dbMap = [
            'mysql' => 'pdo_mysql',
            'sqlite' => 'pdo_sqlite',
            'pgsql' => 'pdo_pgsql',
        ];
        $dbConfig = $dbManager->connection()->getConfig();

        if (array_key_exists($dbConfig['name'], $dbMap)) {
            $dbConfig['driver'] = $dbMap[$dbConfig['name']];
        }

        if ($dbConfig['name'] === 'pgsql') {
            dd($dbConfig);
        }

        $connectionParams = [
            'dbname' => $dbConfig['database'],
            'user' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'host' => $dbConfig['host'],
            'driver' => $dbConfig['driver'],
        ];

        return DriverManager::getConnection($connectionParams);
    }
}
