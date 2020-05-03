<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;


interface FieldMapperInterface
{
    public function map($data): array;
}