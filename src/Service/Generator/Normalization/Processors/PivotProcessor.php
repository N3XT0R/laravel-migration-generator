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
            if (!isset($definitions['primaryKey'])) {
                continue;
            }

            /** @var PrimaryKeyEntity|null $primary */
            $primary = current($definitions['primaryKey']);
            if (!$this->isCompositePrimaryKey($primary)) {
                continue;
            }

            $results[$tableName] = $this->transformPivotTable($tableName, $definitions, $primary);
        }

        $result->setResults($results);
        $context->update($result);

        return $result;
    }

    protected function isCompositePrimaryKey(?PrimaryKeyEntity $primary): bool
    {
        return $primary instanceof PrimaryKeyEntity && count($primary->getColumns()) >= 2;
    }

    protected function transformPivotTable(string $tableName, array $definitions, PrimaryKeyEntity $primary): array
    {
        $definitions['table'] = $this->prependIdField($tableName, $definitions['table']);
        unset($definitions['primaryKey']);
        $definitions['index'][] = $this->createUniqueIndexFromPrimaryKey($primary);

        return $definitions;
    }

    protected function prependIdField(string $tableName, array $fields): array
    {
        $idField = new FieldEntity();
        $idField->setTable($tableName);
        $idField->setType('bigInteger');
        $idField->setArguments(['autoIncrement' => true]);
        $idField->setOptions([
            'default' => null,
            'unsigned' => true,
            'nullable' => false,
        ]);
        $idField->setColumnName('id');

        return ['id' => $idField] + $fields;
    }

    protected function createUniqueIndexFromPrimaryKey(PrimaryKeyEntity $primary): IndexEntity
    {
        $index = new IndexEntity();
        $index->setType('unique');
        $index->setIndexType('index');
        $index->setColumns($primary->getColumns());
        $index->setName(implode('_', $primary->getColumns()) . '_unique');

        return $index;
    }
}