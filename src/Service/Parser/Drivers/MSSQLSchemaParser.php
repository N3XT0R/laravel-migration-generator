<?php

namespace N3XT0R\MigrationGenerator\Service\Parser\Drivers;

use N3XT0R\MigrationGenerator\Service\Parser\AbstractSchemaParser;

class MSSQLSchemaParser extends AbstractSchemaParser
{
    public function getTablesFromSchema(string $schema): array
    {
        $tables = [];
        $queryResult = $this->getConnection()->select(
            'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_CATALOG = ? AND TABLE_TYPE = ? AND TABLE_NAME != ?',
            [$schema, 'BASE TABLE', 'migrations']
        );

        foreach ($queryResult as $result) {
            $tables[] = $result->TABLE_NAME;
        }

        return $tables;
    }

    protected function getForeignKeyConstraints(string $schema, string $tableName): array
    {
        return $this->getConnection()->select(
            "
            SELECT
                CONSTRAINT_NAME AS name
            FROM
                INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE
                CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND TABLE_CATALOG = ?
                AND TABLE_NAME = ?
            ",
            [$schema, $tableName]
        );
    }

    protected function getRefNameByConstraintName(string $schema, string $constraintName): string
    {
        $conn = $this->getConnection();

        $r = $conn->selectOne("
            SELECT
                kcu2.TABLE_NAME AS refname
            FROM
                INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc
                INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu1
                    ON rc.CONSTRAINT_NAME = kcu1.CONSTRAINT_NAME
                INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu2
                    ON rc.UNIQUE_CONSTRAINT_NAME = kcu2.CONSTRAINT_NAME
            WHERE
                kcu1.TABLE_CATALOG = ?
                AND kcu1.CONSTRAINT_NAME = ?
            ",
            [$schema, $constraintName]
        );

        return $r?->refname ?? '';
    }
}
