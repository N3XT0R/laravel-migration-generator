<?php


namespace N3XT0R\MigrationGenerator\Service\Parser;


interface SchemaParserInterface
{
    public function getTablesFromSchema(string $schema): array;

    public function getSortedTablesFromSchema(string $schema): array;

    public function setConnectionByName(string $connectionName = ''): void;

}