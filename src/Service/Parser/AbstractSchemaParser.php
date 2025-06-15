<?php

namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;

abstract class AbstractSchemaParser implements SchemaParserInterface
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->setConnection($connection);
    }

    public function setConnection(ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}