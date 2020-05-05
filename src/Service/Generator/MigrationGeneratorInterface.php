<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

interface MigrationGeneratorInterface
{
    public function __construct(DefinitionResolverInterface $resolver, MigrationCompilerInterface $compiler);

    public function setMigrationDir(string $migrationDir): void;

    public function getMigrationDir(): string;

    public function generateMigrationForTable(string $database, string $table): bool;

    public function getMigrationFiles(): array;

    public function setMigrationFiles(array $migrationFiles): void;

    public function getErrorMessages(): array;

    public function setErrorMessages(array $errorMessages): void;

    public function addErrorMessage(string $errorMessage): void;
}