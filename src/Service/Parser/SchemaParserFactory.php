<?php

namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\MSSQLSchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\MySQLSchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\PostgresSchemaParser;

class SchemaParserFactory implements SchemaParserFactoryInterface
{
    public static function create(ConnectionInterface $connection): SchemaParserInterface
    {
        $driver = $connection->getDriverName();

        return match ($driver) {
            'mysql' => new MySQLSchemaParser($connection),
            'pgsql' => new PostgresSchemaParser($connection),
            'sqlsrv' => new MSSQLSchemaParser($connection),
            default => throw new \InvalidArgumentException("Unsupported database driver: {$driver}"),
        };
    }
}