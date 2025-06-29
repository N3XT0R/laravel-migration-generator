<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;

class PivotProcessor implements ProcessorInterface
{
    public function process(NormalizationContext $context): ResultEntity
    {
        $result = $context->getCurrent();
        $results = $result->getResults();

        foreach ($results as $tableName => $definitions) {
            $primary = current($definitions['primaryKey']);
            if ($primary instanceof PrimaryKeyEntity && count($primary->getColumns()) >= 2) {
                $idField = new FieldEntity();
                $idField->setTable($tableName);
                $idField->setType('bigInteger');
                $idField->setArguments(['autoIncrement' => true]);
                $idField->setOptions([
                    'default' => null,
                    'unsigned' => true,
                    'nullable' => false
                ]);

                $idField->setColumnName('id');
                $results[$tableName]['table'] = ['id' => $idField, ...$definitions['table']];
                unset($results[$tableName]['primaryKey']);
            }
        }

        $result->setResults($results);
        $context->update($result);
        return $result;
    }
}