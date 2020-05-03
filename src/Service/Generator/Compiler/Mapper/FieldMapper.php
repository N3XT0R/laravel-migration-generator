<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;

class FieldMapper extends AbstractMapper
{
    public function map(array $data): array
    {
        $result = [];
        foreach ($data as $field) {
            if ($field instanceof FieldEntity) {
                $column = '';
            }
        }


        return $result;
    }

    protected function generate(FieldEntity $fieldEntity): string
    {
        return '';
    }
}