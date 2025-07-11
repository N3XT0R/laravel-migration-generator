<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\DTO\MigrationTimingDto;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManagerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

interface MigrationGeneratorInterface
{

    public function setMigrationDir(string $migrationDir): void;

    public function setResolver(DefinitionResolverInterface $resolver): void;

    public function getResolver(): DefinitionResolverInterface;

    public function setCompiler(MigrationCompilerInterface $compiler): void;

    public function getCompiler(): MigrationCompilerInterface;

    public function getNormalizationManager(): ?SchemaNormalizationManagerInterface;

    public function setNormalizationManager(?SchemaNormalizationManagerInterface $normalizationManager): void;

    public function getMigrationDir(): string;

    public function generateMigrationForTable(
        string $database,
        string $table,
        MigrationTimingDto $timingDto,
    ): bool;

    public function getMigrationFiles(): array;

    public function setMigrationFiles(array $migrationFiles): void;

    public function getErrorMessages(): array;

    public function setErrorMessages(array $errorMessages): void;

    public function addErrorMessage(string $errorMessage): void;
}