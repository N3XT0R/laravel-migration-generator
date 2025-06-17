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
        $tables = $this->getTablesFromSchema($schema);
        sort($tables);
        return $this->sortTablesByConstraintsRecursive($schema, $tables);
    }

    private function sortTablesByConstraintsRecursive(string $schema, array $tables, array $sortedTables = []): array
    {
        $unsortedTables = [];
        $tablesToAdd = [];

        // Sortiere Input immer
        sort($tables);

        foreach ($tables as $tableName) {
            $constraints = $this->getForeignKeyConstraints($schema, $tableName);

            if (empty($constraints) || $this->hasAllReferencedTables($schema, $constraints, $sortedTables)) {
                $tablesToAdd[] = $tableName;
            } else {
                $unsortedTables[] = $tableName;
            }
        }

        // Sortiere auch die Tabellen, die jetzt hinzugefügt werden, alphabetisch
        sort($tablesToAdd);

        foreach ($tablesToAdd as $tableName) {
            if (!in_array($tableName, $sortedTables, true)) {
                $sortedTables[] = $tableName;
            }
        }

        if (!empty($unsortedTables)) {
            $sortedTables = $this->sortTablesByConstraintsRecursive($schema, $unsortedTables, $sortedTables);
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