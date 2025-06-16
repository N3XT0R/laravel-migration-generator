<?php


namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;

class SchemaParser extends AbstractSchemaParser
{
    public function getTablesFromSchema(string $schema): array
    {
        $tables = [];
        $connection = $this->getConnection();
        $queryResult = $connection->select(
            'SELECT * FROM information_schema.tables WHERE `table_schema` =  ? and `table_type` = ? AND `TABLE_NAME` != ?;',
            [$schema, 'BASE TABLE', 'migrations']
        );

        foreach ($queryResult as $result) {
            $tables[] = $result->TABLE_NAME;
        }

        return $tables;
    }

    public function getSortedTablesFromSchema(string $schema): array
    {
        return $this->sortTablesByConstraintsRecursive($schema, $this->getTablesFromSchema($schema));
    }

    private function getForeignKeyConstraints(string $schema, string $tableName): array
    {
        return $this->getConnection()->select(
            "
        SELECT `CONSTRAINT_NAME` AS `name`
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE `CONSTRAINT_TYPE` = 'FOREIGN KEY'
        AND `CONSTRAINT_SCHEMA` = ?
        AND `TABLE_NAME` = ?
        ",
            [$schema, $tableName]
        );
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

    private function sortTablesByConstraintsRecursive(string $schema, array $tables, array $sortedTables = []): array
    {
        $unsortedTables = [];

        foreach ($tables as $tableName) {
            $constraints = $this->getForeignKeyConstraints($schema, $tableName);

            // If the table has no foreign key constraints
            // or all referenced tables are already sorted, add it to the sorted list
            if (empty($constraints) || $this->hasAllReferencedTables($schema, $constraints, $sortedTables)) {
                $sortedTables[] = $tableName;
            } else {
                // Otherwise, keep it for the next sorting iteration
                $unsortedTables[] = $tableName;
            }
        }

        if (!empty($unsortedTables)) {
            // Recursively sort the remaining unsorted tables
            $sorted = $this->sortTablesByConstraintsRecursive($schema, $unsortedTables, $sortedTables);
            // Merge the newly sorted tables while preserving order
            $sortedTables = array_merge($sortedTables, $sorted);
        }

        // Remove duplicates and sort alphabetically to ensure deterministic order for tests
        $sortedTables = array_unique($sortedTables);
        sort($sortedTables);

        return $sortedTables;
    }

    private function getRefNameByConstraintName(string $schema, string $constraintName): string
    {
        $conn = $this->getConnection();
        $version = $this->getMysqlVersion($conn);

        if (version_compare($version, '8.0.0', '>=')) {
            $r = $conn->selectOne("
            SELECT kcu.referenced_table_name AS refName
            FROM information_schema.referential_constraints rc
            JOIN information_schema.key_column_usage kcu
              ON rc.constraint_name = kcu.constraint_name
             AND rc.constraint_schema = kcu.constraint_schema
            WHERE rc.constraint_schema = ? AND rc.constraint_name = ? LIMIT 1
        ", [$schema, $constraintName]);
            return $r?->refName ?? '';
        }

        $id = "$schema/$constraintName";
        $r = $conn->selectOne("
        SELECT REPLACE(REF_NAME, ?, '') AS refName
        FROM information_schema.INNODB_SYS_FOREIGN
        WHERE id = ?
    ", ["$schema/", $id]);

        return $r?->refName ?? '';
    }

    private function getMysqlVersion(ConnectionInterface $connection): string
    {
        return $connection->selectOne('SELECT VERSION() as version')->version;
    }
}