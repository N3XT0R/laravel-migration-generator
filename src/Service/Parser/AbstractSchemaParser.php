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

    public function getSortedTablesFromSchema(string $schema): array
    {
        return $this->sortTablesByConstraintsRecursive($schema, $this->getTablesFromSchema($schema));
    }

    private function sortTablesByConstraintsRecursive(string $schema, array $tables, array $sortedTables = []): array
    {
        $unsortedTables = [];

        foreach ($tables as $tableName) {
            $constraints = $this->getForeignKeyConstraints($schema, $tableName);

            if (empty($constraints) || $this->hasAllReferencedTables($schema, $constraints, $sortedTables)) {
                $sortedTables[] = $tableName;
            } else {
                $unsortedTables[] = $tableName;
            }
        }

        if (!empty($unsortedTables)) {
            $sorted = $this->sortTablesByConstraintsRecursive($schema, $unsortedTables, $sortedTables);
            $sortedTables = array_replace_recursive($sortedTables, $sorted);
        }

        return $sortedTables;
    }

    private function hasAllReferencedTables(string $schema, array $constraints, array $sortedTables): bool
    {
        foreach ($constraints as $constraint) {
            $refName = $this->getRefNameByConstraintName($schema, $constraint->name);
            if (!in_array($refName, $sortedTables, true)) {
                return false;
            }
        }

        return true;
    }

    abstract public function getTablesFromSchema(string $schema): array;

    abstract protected function getForeignKeyConstraints(string $schema, string $tableName): array;

    abstract protected function getRefNameByConstraintName(string $schema, string $constraintName): string;
}