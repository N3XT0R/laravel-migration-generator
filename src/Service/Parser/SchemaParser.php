<?php


namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

class SchemaParser implements SchemaParserInterface
{
    protected ConnectionInterface $connection;

    public function __construct(string $connectionName = '')
    {
        $this->setConnectionByName($connectionName);
    }

    public function setConnectionByName(string $connectionName = ''): void
    {
        if (empty($connectionName)) {
            $connectionName = DB::getDefaultConnection();
        }

        $this->setConnection(DB::connection($connectionName));
    }

    public function setConnection(ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }


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

    private function getRefNameByConstraintName(string $schema, string $constraintName): string
    {
        $connection = $this->getConnection();
        $id = $schema . '/' . $constraintName;
        $result = $connection->selectOne(
            "SELECT replace(REF_NAME, ?, '') refName FROM information_schema.INNODB_SYS_FOREIGN where id = ? ",
            [$schema . '/', $id]
        );

        return $result->refName;
    }
}