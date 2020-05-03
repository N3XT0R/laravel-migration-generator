<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;

class IndexMapper extends AbstractMapper
{
    public function map(array $data): array
    {
        $result = [];
        foreach ($data as $field) {
            if ($field instanceof IndexEntity) {
                $result[] = $this->generateIndex($field);
            }
        }

        return $result;
    }

    public function generateIndex(IndexEntity $index): string
    {
        if ('unique' === $index->getType()) {
            $method = 'unique(';
        } else {
            $method = 'index(';
        }

        $columns = $index->getColumns();
        if (1 < count($columns)) {
            $method .= "['" . implode("', '", $columns) . "']";
        } else {
            $method .= "'" . $columns[0] . "'";
        }

        $method .= ", '" . $index->getName() . "')";

        $methods = [$method];

        return $this->chainMethodsToString($methods);
    }

}