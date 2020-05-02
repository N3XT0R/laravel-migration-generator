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
        $fields = $this->getAttributeByName('table');
        $foreignKeys = $schema->listTableForeignKeys($table);

        $result = [];
        if (0 !== count($foreignKeys)) {
            $result = $this->generateForeignKeys($table, $fields, $foreignKeys);
        }

        return $result;
    }

    protected function generateForeignKeys(string $table, array $fields, array $foreignKeys): array
    {
        $result = [];

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey instanceof ForeignKeyConstraint === false) {
                continue;
            }
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

            if (array_key_exists($localColumn, $fields)) {
                /**
                 * @var FieldEntity $fieldEntity
                 */
                $fieldEntity = $fields[$localColumn];
                $fieldEntity->addForeignKey($foreignKeyEntity);
            } else {
                $result[] = $foreignKeyEntity;
            }
        }

        return $result;
    }
}