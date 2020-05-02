<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;


interface DefinitionResolverInterface
{
    public function resolveTableSchema(string $schema, string $table): array;
}