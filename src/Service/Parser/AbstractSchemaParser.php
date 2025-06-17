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
        return $this->sortTablesTopologically($schema, $tables);
    }

    private function sortTablesByConstraintsRecursive(
        string $schema,
        array $tables,
        array $sortedTables = [],
        array &$sortedSet = []
    ): array {
        $unsortedTables = [];
        $tablesToAdd = [];

        foreach ($tables as $tableName) {
            $constraints = $this->getForeignKeyConstraints($schema, $tableName);

            if (empty($constraints) || $this->hasAllReferencedTables($schema, $constraints, $sortedSet)) {
                $tablesToAdd[] = $tableName;
            } else {
                $unsortedTables[] = $tableName;
            }
        }

        sort($tablesToAdd);

        foreach ($tablesToAdd as $tableName) {
            if (!isset($sortedSet[$tableName])) {
                $sortedTables[] = $tableName;
                $sortedSet[$tableName] = true;
            }
        }

        if (!empty($unsortedTables)) {
            $sorted = $this->sortTablesByConstraintsRecursive($schema, $unsortedTables, $sortedTables, $sortedSet);
            foreach ($sorted as $tableName) {
                if (!isset($sortedSet[$tableName])) {
                    $sortedTables[] = $tableName;
                    $sortedSet[$tableName] = true;
                }
            }
        }

        return $sortedTables;
    }

    private function sortTablesTopologically(string $schema, array $tables): array
    {
        // Schritt 1: Baue Abhängigkeitsgraph (Tabelle => Tabellen, von denen sie abhängt)
        $dependencies = [];
        $dependents = [];
        $inDegree = [];

        foreach ($tables as $table) {
            $constraints = $this->getForeignKeyConstraints($schema, $table);

            $deps = [];
            foreach ($constraints as $constraint) {
                $refTable = $this->getRefNameByConstraintName($schema, $constraint->name);
                if ($refTable && in_array($refTable, $tables, true)) {
                    $deps[] = $refTable;
                    $dependents[$refTable][] = $table;
                }
            }
            $dependencies[$table] = $deps;
            $inDegree[$table] = count($deps);
        }

        // Schritt 2: Finde alle Tabellen ohne Abhängigkeiten (InDegree 0)
        $queue = [];
        foreach ($inDegree as $table => $degree) {
            if ($degree === 0) {
                $queue[] = $table;
            }
        }

        // Schritt 3: Iterativ sortieren
        $sorted = [];
        while (!empty($queue)) {
            sort($queue); // Alphabetisch stabilisieren, falls gewünscht
            $table = array_shift($queue);
            $sorted[] = $table;

            // Entferne Kanten: Für alle abhängigen Tabellen InDegree reduzieren
            if (isset($dependents[$table])) {
                foreach ($dependents[$table] as $dependent) {
                    $inDegree[$dependent]--;
                    if ($inDegree[$dependent] === 0) {
                        $queue[] = $dependent;
                    }
                }
            }
        }

        // Schritt 4: Zyklus prüfen — falls noch Tabellen mit InDegree > 0
        $remaining = array_filter($inDegree, fn($degree) => $degree > 0);
        if (!empty($remaining)) {
            throw new \RuntimeException('Cycle detected in foreign key constraints involving tables: '.implode(', ',
                    array_keys($remaining)));
        }

        return $sorted;
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