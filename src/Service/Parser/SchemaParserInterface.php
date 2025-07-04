<?php


namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;


interface SchemaParserInterface
{
    public function getTablesFromSchema(string $schema): array;

    public function getSortedTablesFromSchema(string $schema): array;

    public function setConnectionByName(string $connectionName = ''): void;

    public function setConnection(?ConnectionInterface $connection): void;

    public function getConnection(): ConnectionInterface;
    

}