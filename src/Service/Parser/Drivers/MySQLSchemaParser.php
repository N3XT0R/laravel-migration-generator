<?php

namespace N3XT0R\MigrationGenerator\Service\Parser\Drivers;

use Illuminate\Database\ConnectionInterface;
use N3XT0R\MigrationGenerator\Service\Parser\AbstractSchemaParser;

class MySQLSchemaParser extends AbstractSchemaParser
{
    public function getTablesFromSchema(string $schema): array
    {
        $tables = [];
        $queryResult = $this->getConnection()->select(
            'SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_type = ? AND table_name != ?',
            [$schema, 'BASE TABLE', 'migrations']
        );

        foreach ($queryResult as $result) {
            $tables[] = $result->TABLE_NAME;
        }

        return $tables;
    }

    private function getMysqlVersion(ConnectionInterface $connection): string
    {
        return $connection->selectOne('SELECT VERSION() as version')->version;
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
}