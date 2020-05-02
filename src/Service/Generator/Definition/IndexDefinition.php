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
        $tableResult = $this->getAttributeByName('table');
        var_dump($this->getAttributes());
        die();
        $schema = $this->getSchema();

        $result = [];

        if (count($tableResult) !== 0) {
            $result = $this->generateIndexes($tableResult, $schema->listTableIndexes($table));
        }

        return $result;
    }


    protected function generateIndexes(array $fields, array $indexes): array
    {
        $combinedIndexes = [];
        foreach ($indexes as $index) {
            if ($index instanceof Index === false) {
                continue;
            }

            $fieldEntity = null;
            $columns = $index->getColumns();
            $isCombinedIndex = count($columns) > 1;

            switch (true) {
                case true === $index->isUnique():
                    $type = 'unique';
                    break;

                case true === $index->isPrimary():
                    $type = 'primary';
                    break;

                default:
                    $type = 'index';
                    break;
            }

            $indexEntity = new IndexEntity();
            $indexEntity->setType($type);
            $indexEntity->setName($index->getName());

            if (false === $isCombinedIndex) {
                $column = current($columns);
                /**
                 * @var FieldEntity $fieldEntity
                 */
                if ($column === 'email') {
                    var_dump($fields);
                    die();
                }
                $fieldEntity = $fields[$column];
                $fieldEntity->addIndex($indexEntity);
            } else {
                $indexEntity->setColumns($columns);
                $combinedIndexes[] = $indexEntity;
            }
        }

        return $combinedIndexes;
    }

}