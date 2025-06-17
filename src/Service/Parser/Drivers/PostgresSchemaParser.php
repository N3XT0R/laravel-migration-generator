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
        $result = $this->getConnection()->select(
            '
            SELECT
                tc.constraint_name AS name
            FROM
                information_schema.table_constraints tc
            WHERE
                tc.constraint_type = \'FOREIGN KEY\'
                AND tc.table_catalog = ?
                AND tc.table_name = ?
                AND tc.table_schema = ?
            ',
            [$schema, $tableName, 'public']
        );
        return $result;
    }

    protected function getRefNameByConstraintName(string $schema, string $constraintName): string
    {
        $conn = $this->getConnection();

        $result = $conn->selectOne("
        SELECT
            c_rel.relname AS refName
        FROM
            pg_constraint con
            JOIN pg_class tbl ON con.conrelid = tbl.oid
            JOIN pg_class c_rel ON con.confrelid = c_rel.oid
            JOIN pg_namespace ns ON ns.oid = tbl.relnamespace
        WHERE
            con.contype = 'f' -- foreign key constraint
            AND ns.nspname = ?
            AND con.conname = ?
        LIMIT 1
    ", [$schema, $constraintName]);

        return $result?->refName ?? '';
    }
}
