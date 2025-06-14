<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;


use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;

class ForeignKeyDefinition extends AbstractDefinition
{
    protected function generateData(): array
    {
        $table = $this->getAttributeByName('tableName');
        $schema = $this->getSchema();
        $tableResult = (array) $this->getAttributeByName('table');
        $result = [];
        if (0 !== count($tableResult)) {
            $result = $this->generateForeignKeys($table, $schema->listTableForeignKeys($table));
        }

        return $result;
    }

    protected function generateForeignKeys(string $table, array $foreignKeys): array
    {
        $result = [];

        foreach ($foreignKeys as $foreignKey) {
            if (!($foreignKey instanceof ForeignKeyConstraint)) {
                continue;
            }

            $entity = new ForeignKeyEntity();
            $entity->setName($foreignKey->getName());
            $entity->setLocalTable($table);
            $entity->setLocalColumn($foreignKey->getLocalColumns()[0]);
            $entity->setReferencedTable($foreignKey->getForeignTableName());
            $entity->setReferencedColumn($foreignKey->getForeignColumns()[0]);

            if ($foreignKey->hasOption('onUpdate')) {
                $entity->setOnUpdate($foreignKey->getOption('onUpdate'));
            }

            if ($foreignKey->hasOption('onDelete')) {
                $entity->setOnDelete($foreignKey->getOption('onDelete'));
            }

            $result[] = $entity;
        }

        return $result;
    }
}