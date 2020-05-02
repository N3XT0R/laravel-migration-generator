<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

interface DefinitionResolverInterface
{
    public function resolveTableSchema(string $schema, string $table): ResultEntity;
}