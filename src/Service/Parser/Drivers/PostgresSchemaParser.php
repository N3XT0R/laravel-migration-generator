<?php

namespace N3XT0R\MigrationGenerator\Service\Parser\Drivers;

use N3XT0R\MigrationGenerator\Service\Parser\AbstractSchemaParser;

class PostgresSchemaParser extends AbstractSchemaParser
{
    public function getTablesFromSchema(string $schema): array
    {
        $tables = [];
        /**
         * TODO:
         * Implement an additional parameter to allow selection of the `table_catalog` (database name).
         * Currently, the query only filters by `table_schema`.
         * For multi-database scenarios, passing the catalog dynamically is necessary
         * to enable querying tables across different databases accurately.
         */
        $queryResult = $this->getConnection()->select(
            'SELECT table_name 
         FROM information_schema.tables 
         WHERE table_catalog = ?
           AND table_type = ? 
           AND table_name != ? 
           AND table_schema = ?',
            [$schema, 'BASE TABLE', 'migrations', 'public']
        );

        foreach ($queryResult as $result) {
            // Bei Postgres ist das Feld "table_name" in Kleinbuchstaben
            $tables[] = $result->table_name;
        }

        return $tables;
    }

    protected function getForeignKeyConstraints(string $schema, string $tableName): array
    {
        return $this->getConnection()->select(
            '
            SELECT
                tc.constraint_name AS name
            FROM
                information_schema.table_constraints tc
            WHERE
                tc.constraint_type = \'FOREIGN KEY\'
                AND tc.table_schema = ?
                AND tc.table_name = ?
                AND tc.table_catalog = ?
            ',
            [$schema, $tableName, 'public']
        );
    }

    protected function getRefNameByConstraintName(string $schema, string $constraintName): string
    {
        $conn = $this->getConnection();
        /**
         * TODO:
         * Implement an additional parameter to allow selection of the `table_catalog` (database name).
         * Currently, the query only filters by `table_schema`.
         * For multi-database scenarios, passing the catalog dynamically is necessary
         * to enable querying tables across different databases accurately.
         */
        $r = $conn->selectOne("
            SELECT
                kcu.referenced_table_name AS refName
            FROM
                information_schema.referential_constraints rc
                JOIN information_schema.key_column_usage kcu
                    ON rc.constraint_name = kcu.constraint_name
                    AND rc.constraint_schema = kcu.constraint_schema
            WHERE
                rc.constraint_schema = ?
                AND rc.constraint_name = ?
            LIMIT 1
        ", [$schema, $constraintName]);

        return $r?->refName ?? '';
    }
}
