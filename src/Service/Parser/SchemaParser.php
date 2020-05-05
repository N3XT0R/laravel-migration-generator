<?php


namespace N3XT0R\MigrationGenerator\Service\Parser;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

class SchemaParser implements SchemaParserInterface
{
    protected $connection;

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

    private function sortTablesByConstraintsRecursive(string $schema, array $tables, array $sortedTables = []): array
    {
        $unsortedTables = [];
        $connection = $this->getConnection();
        foreach ($tables as $tableName) {
            $result = $connection->select(
                "
                select `CONSTRAINT_NAME` as `name` 
                from information_schema.TABLE_CONSTRAINTS
                where `CONSTRAINT_TYPE` = 'foreign key' 
                AND `CONSTRAINT_SCHEMA`  = ?
                AND `TABLE_NAME` = ?
            ",
                [$schema, $tableName]
            );
            if (0 === count($result)) {
                $sortedTables[] = $tableName;
            } else {
                $alreadyHaveAllForeignTables = true;
                foreach ($result as $constraint) {
                    $refName = $this->getRefNameByConstraintName($schema, $constraint->name);
                    if (!in_array($refName, $sortedTables, true)) {
                        $alreadyHaveAllForeignTables = false;
                        break;
                    }
                }

                if (true === $alreadyHaveAllForeignTables) {
                    $sortedTables[] = $tableName;
                } else {
                    $unsortedTables[] = $tableName;
                }
            }
        }

        if (0 !== count($unsortedTables)) {
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