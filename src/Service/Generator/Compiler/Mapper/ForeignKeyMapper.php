<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;

class ForeignKeyMapper extends AbstractMapper
{
    public function map(array $data): array
    {
        $result = [];
        foreach ($data as $foreignKey) {
            if ($foreignKey instanceof ForeignKeyEntity) {
                $result[] = $this->generateForeign($foreignKey);
            }
        }

        return $result;
    }

    protected function generateForeign(ForeignKeyEntity $foreignKey): string
    {
        $methods = [];

        return $this->chainMethodsToString($methods);
    }

}