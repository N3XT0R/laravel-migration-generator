<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;


use Doctrine\DBAL\Schema\Index;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;

class IndexDefinition extends AbstractDefinition
{
    protected function generateData(): array
    {
        $table = $this->getAttributeByName('tableName');
        $tableResult = (array)$this->getAttributeByName('table');
        $schema = $this->getSchema();

        $result = [];
        if (count($tableResult) !== 0) {
            $result = $this->generateIndexes($schema->listTableIndexes($table));
        }

        return $result;
    }


    protected function generateIndexes(array $indexes): array
    {
        $combinedIndexes = [];
        foreach ($indexes as $index) {
            if ($index instanceof Index === false || $index->getName() === 'PRIMARY') {
                continue;
            }

            $fieldEntity = null;
            $columns = $index->getColumns();

            if ($index->isUnique()) {
                $type = 'unique';
            } else {
                $type = 'index';
            }

            $indexEntity = new IndexEntity();
            $indexEntity->setType($type);
            $indexEntity->setName($index->getName());
            $indexEntity->setColumns($columns);
            $combinedIndexes[$index->getName()] = $indexEntity;
        }

        return $combinedIndexes;
    }

}