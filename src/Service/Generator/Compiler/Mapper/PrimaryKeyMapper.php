<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;

class PrimaryKeyMapper extends AbstractMapper
{
    public function map(array $data): array
    {
        $result = [];
        foreach ($data as $field) {
            if ($field instanceof PrimaryKeyEntity) {
                $result[] = $this->generatePrimary($field);
            }
        }

        return $result;
    }

    public function generatePrimary(PrimaryKeyEntity $index): string
    {
        $columns = $index->getColumns();
        $columnList = "['" . implode("', '", $columns) . "']";

        $methodCall = !empty($index->getName())
            ? "primary($columnList, '" . $index->getName() . "')"
            : "primary($columnList)";

        return $this->chainMethodsToString([
            $methodCall
        ]);
    }
}