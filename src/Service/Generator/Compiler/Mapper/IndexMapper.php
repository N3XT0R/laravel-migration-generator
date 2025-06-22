<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;

class IndexMapper extends AbstractMapper
{
    /**
     * @param IndexEntity[] $data
     * @param string[] $foreignKeyNames
     * @return string[]
     */
    public function map(array $data): array
    {
        $result = [];
        $foreignKeyNames = [];
        foreach ($data as $field) {
            if ($field instanceof ForeignKeyEntity) {
                $foreignKeyNames[] = $field->getName();
            }

            if (!$field instanceof IndexEntity) {
                continue;
            }


            if (in_array($field->getName(), $foreignKeyNames, true)) {
                continue;
            }

            $result[] = $this->generateIndex($field);
        }

        return $result;
    }

    protected function generateIndex(IndexEntity $index): string
    {
        $method = $index->getType() === 'unique' ? 'unique(' : 'index(';

        $columns = $index->getColumns();
        if (count($columns) > 1) {
            $method .= "['" . implode("', '", $columns) . "']";
        } else {
            $method .= "'" . $columns[0] . "'";
        }

        $method .= ", '" . $index->getName() . "')";
        $methods = [$method];

        return $this->chainMethodsToString($methods);
    }
}
