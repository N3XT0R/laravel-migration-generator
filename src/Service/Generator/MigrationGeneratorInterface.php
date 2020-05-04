<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

interface MigrationGeneratorInterface
{
    public function __construct(DefinitionResolverInterface $resolver, MigrationCompilerInterface $compiler);

    public function generateMigrationForTable(string $database, string $table): bool;
}