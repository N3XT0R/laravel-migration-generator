<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;

class PivotProcessor implements ProcessorInterface
{

    public function getKey(): string
    {
        return 'pivot';
    }

    public function process(NormalizationContext $context): ResultEntity
    {
        $result = $context->getCurrent();
        $results = $result->getResults();

        foreach ($results as $tableName => $definitions) {
            $primary = current($definitions['primaryKey']);
            $pkColumns = $primary->getColumns();
            if ($primary instanceof PrimaryKeyEntity && count($pkColumns) >= 2) {
                $idField = new FieldEntity();
                $idField->setTable($tableName);
                $idField->setType('bigInteger');
                $idField->setArguments(['autoIncrement' => true]);
                $idField->setOptions([
                    'default' => null,
                    'unsigned' => true,
                    'nullable' => false
                ]);

                $uniqueIndex = new IndexEntity();
                $uniqueIndex->setType('unique');
                $uniqueIndex->setColumns($pkColumns);

                $idField->setColumnName('id');
                $results[$tableName]['table'] = ['id' => $idField, ...$definitions['table']];
                $results[$tableName]['index'] = [$uniqueIndex, ...$definitions['index']];
                unset($results[$tableName]['primaryKey']);
                //dump($results);
            }
        }

        $result->setResults($results);
        $context->update($result);
        return $result;
    }
}