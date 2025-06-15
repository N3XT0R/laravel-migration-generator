<?php

namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

abstract class AbstractSchemaParser implements SchemaParserInterface
{
    protected ?ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection = null)
    {
        $this->setConnection($connection);
    }

    public function setConnectionByName(string $connectionName = ''): void
    {
        if (empty($connectionName)) {
            $connectionName = DB::getDefaultConnection();
        }

        $this->setConnection(DB::connection($connectionName));
    }

    public function setConnection(?ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}