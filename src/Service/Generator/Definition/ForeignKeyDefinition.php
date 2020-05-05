<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;


use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;

class ForeignKeyDefinition extends AbstractDefinition
{
    protected function generateData(): array
    {
        $table = $this->getAttributeByName('tableName');
        $schema = $this->getSchema();
        $tableResult = (array)$this->getAttributeByName('table');
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
            if ($foreignKey instanceof ForeignKeyConstraint) {
                $localColumn = $foreignKey->getLocalColumns()[0];


                $foreignKeyEntity = new ForeignKeyEntity();
                $foreignKeyEntity->setName($foreignKey->getName());
                $foreignKeyEntity->setLocalTable($table);
                $foreignKeyEntity->setLocalColumn($localColumn);
                $foreignKeyEntity->setReferencedTable($foreignKey->getForeignTableName());
                $foreignKeyEntity->setReferencedColumn($foreignKey->getForeignColumns()[0]);

                if ($foreignKey->hasOption('onUpdate')) {
                    $foreignKeyEntity->setOnUpdate($foreignKey->getOption('onUpdate'));
                }

                if ($foreignKey->hasOption('onDelete')) {
                    $foreignKeyEntity->setOnDelete($foreignKey->getOption('onDelete'));
                }

                $result[] = $foreignKeyEntity;
            }
        }

        return $result;
    }
}