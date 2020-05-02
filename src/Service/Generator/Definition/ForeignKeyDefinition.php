<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;


class ForeignKeyDefinition extends AbstractDefinition
{
    protected function generateData(): array
    {
        $table = $this->getAttributeByName('tableName');
        $tableResult = $this->getAttributeByName('table');
        $schema = $this->getSchema();
        $indexes = $schema->listTableForeignKeys($table);
        return [];
    }

    protected function generateForeignKeys(array $fields, array $foreignKeys): array
    {
        return [];
    }
}